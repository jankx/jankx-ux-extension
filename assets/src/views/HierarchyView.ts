import Backbone from 'backbone';
import { ElementNodeCollection } from '@/collections/ElementNodeCollection';
import HierarchyItemView from '@/views/HierarchyItemView';
import type { BuilderNode } from '@/types/index.d';

// Ambient declaration for jQuery UI Sortable (not in @types/jquery)
declare global {
    interface JQuery {
        sortable(options?: Record<string, unknown>): JQuery;
        sortable(method: string): JQuery;
    }
}


interface HierarchyViewOptions extends Backbone.ViewOptions<Backbone.Model> {
    collection: ElementNodeCollection;
    onGear: (node: BuilderNode) => void;
    onAddElement: (parentId?: string) => void;
    onNodesChanged?: (nodes: BuilderNode[]) => void;
}

class HierarchyView extends Backbone.View<Backbone.Model, HTMLElement> {
    private _onGear: (node: BuilderNode) => void;
    private _onAddElement: (parentId?: string) => void;
    private _onNodesChanged?: (nodes: BuilderNode[]) => void;
    private _selectedId: string | null = null;
    private _isDragging: boolean = false;
    declare collection: ElementNodeCollection;

    constructor(options: HierarchyViewOptions) {
        super(options);
        this._onGear = options.onGear;
        this._onAddElement = options.onAddElement;
        this._onNodesChanged = options.onNodesChanged;
        this.setElement(document.getElementById('jux-hierarchy') as HTMLElement);
        this.listenTo(this.collection, 'add remove reset change', this.render.bind(this));

        // Global add button
        this.$el.on('click', '#jux-add-first-element', () => this._onAddElement());
        this.$el.on('click', '.jux-hierarchy-add-btn[data-action="add-global"]', () => this._onAddElement());
    }

    render(): this {
        if (this._isDragging) return this;

        this.$el.empty();

        if (this.collection.length === 0) {
            this.$el.html(`
                <div class="jux-hierarchy-empty">
                    <p>No content yet.</p>
                    <button class="jux-btn-add-inline" id="jux-add-first-element" type="button">
                        <span class="dashicons dashicons-plus"></span>
                        Add elements
                    </button>
                </div>
            `);
            return this;
        }

        // Root sortable container
        const $root = jQuery('<div class="jux-sortable-root jux-sortable-list" data-parent-id="">');

        this.collection.each((model) => {
            const node = model.toJSON() as BuilderNode;
            const itemView = new HierarchyItemView({
                node,
                depth: 0,
                onGear: this._onGear.bind(this),
                onSelect: this._onSelect.bind(this),
                onAddChild: (parentId: string) => this._onAddElement(parentId),
            });
            $root.append(itemView.render().el);
        });

        this.$el.append($root);

        this.$el.append(`
            <button class="jux-hierarchy-add-btn" data-action="add-global" type="button">
                <span class="dashicons dashicons-plus"></span> Add elements
            </button>
        `);

        if (this._selectedId) {
            this.$(`[data-id="${this._selectedId}"] .jux-hierarchy-label`).first()
                .addClass('is-selected');
        }

        this._initSortable();

        return this;
    }

    private _initSortable() {
        // Initialize sortable on all sortable lists (root + children containers)
        const $lists = this.$('.jux-sortable-list');

        $lists.sortable({
            handle: '.jux-hierarchy-label',
            connectWith: '.jux-sortable-list',
            placeholder: 'jux-sortable-placeholder',
            tolerance: 'pointer',
            delay: 150,
            distance: 5,
            opacity: 0.8,
            cursor: 'grabbing',

            start: (_e: unknown, ui: { placeholder: JQuery; item: JQuery }) => {
                this._isDragging = true;
                ui.placeholder.height(ui.item.outerHeight() || 36);
                ui.item.addClass('jux-dragging');
            },

            stop: (_e: unknown, ui: { item: JQuery }) => {
                ui.item.removeClass('jux-dragging');
                this._isDragging = false;

                // Rebuild node tree from DOM order
                const newNodes = this._buildNodesFromDOM();
                this._applyNewNodes(newNodes);

                // Destroy sortable before re-render to avoid stale state
                this.$('.jux-sortable-list').sortable('destroy');
            },

            over: (_e: unknown, ui: { placeholder: JQuery }) => {
                ui.placeholder.closest('.jux-sortable-list').addClass('jux-sortable-over');
            },

            out: (_e: unknown, ui: { placeholder: JQuery }) => {
                ui.placeholder.closest('.jux-sortable-list').removeClass('jux-sortable-over');
            },
        });

        // Make labels look draggable
        this.$('.jux-hierarchy-label').css('cursor', 'grab');
    }

    /**
     * Walk the DOM to reconstruct the node tree after drag & drop.
     * Each .jux-hierarchy-item[data-id] maps to a node;
     * nested .jux-sortable-list inside it = children container.
     */
    private _buildNodesFromDOM(): BuilderNode[] {
        // Build a flat lookup of all nodes by id from the current collection snapshot
        const allNodes = this._flattenNodes(this.collection.toJSON() as BuilderNode[]);

        const readList = ($list: JQuery): BuilderNode[] => {
            const result: BuilderNode[] = [];
            $list.children('.jux-hierarchy-item').each((_i, el) => {
                const id = jQuery(el).attr('data-id');
                if (!id) return;

                const nodeData = allNodes[id];
                if (!nodeData) return;

                // Check for a nested sortable list (children)
                const $childList = jQuery(el).children('.jux-hierarchy-children').children('.jux-sortable-list');
                const children = $childList.length > 0 ? readList($childList) : (nodeData.children ?? null);

                result.push({ ...nodeData, children });
            });
            return result;
        };

        return readList(this.$('.jux-sortable-root'));
    }

    /** Flatten all nodes recursively into a id→node map */
    private _flattenNodes(nodes: BuilderNode[]): Record<string, BuilderNode> {
        const map: Record<string, BuilderNode> = {};
        const walk = (list: BuilderNode[]) => {
            list.forEach((n) => {
                map[n.id] = n;
                if (n.children) walk(n.children);
            });
        };
        walk(nodes);
        return map;
    }

    private _applyNewNodes(nodes: BuilderNode[]) {
        // Temporarily stop re-render while we reset
        this.stopListening(this.collection);

        this.collection.reset(nodes);

        // Restart listening
        this.listenTo(this.collection, 'add remove reset change', this.render.bind(this));

        // Notify parent (BuilderApp) to push history + update preview
        if (this._onNodesChanged) {
            this._onNodesChanged(nodes);
        }

        // Re-render hierarchy
        this.render();
    }

    private _onSelect(node: BuilderNode, $label: JQuery) {
        this.$('.jux-hierarchy-label').removeClass('is-selected');
        $label.addClass('is-selected');
        this._selectedId = node.id;

        const frame = document.getElementById('jux-preview-frame') as HTMLIFrameElement | null;
        frame?.contentWindow?.postMessage({ action: 'jux-select', id: node.id }, '*');
    }
}

export default HierarchyView;
