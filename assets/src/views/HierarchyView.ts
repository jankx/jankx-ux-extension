import Backbone from 'backbone';
import { ElementNodeCollection } from '@/collections/ElementNodeCollection';
import HierarchyItemView from '@/views/HierarchyItemView';
import type { BuilderNode } from '@/types/index.d';

interface HierarchyViewOptions extends Backbone.ViewOptions<Backbone.Model> {
    collection: ElementNodeCollection;
    onGear: (node: BuilderNode) => void;
    onAddElement: (parentId?: string) => void;
}

class HierarchyView extends Backbone.View<Backbone.Model, HTMLElement> {
    private _onGear: (node: BuilderNode) => void;
    private _onAddElement: (parentId?: string) => void;
    private _selectedId: string | null = null;
    declare collection: ElementNodeCollection;

    events = {
        'click #jux-add-first-element': 'onAddFirst',
        'click .jux-hierarchy-add-btn[data-action="add-global"]': 'onAddGlobal',
    };

    constructor(options: HierarchyViewOptions) {
        super(options);
        this._onGear = options.onGear;
        this._onAddElement = options.onAddElement;
        this.el = document.getElementById('jux-hierarchy') as HTMLElement;
        this.$el = jQuery(this.el);
        this.listenTo(this.collection, 'add remove reset change', this.render.bind(this));
    }

    render(): this {
        this.$el.empty();

        if (this.collection.length === 0) {
            this.$el.html(`
                <div class="jux-hierarchy-empty">
                    <p>No content yet.</p>
                    <button class="jux-btn-add-inline" id="jux-add-first-element">
                        <span class="dashicons dashicons-plus"></span>
                        Add elements
                    </button>
                </div>
            `);
            return this;
        }

        this.collection.each((model) => {
            const node = model.toJSON() as BuilderNode;
            const itemView = new HierarchyItemView({
                node,
                depth: 0,
                onGear: this._onGear.bind(this),
                onSelect: this._onSelect.bind(this),
                onAddChild: (parentId: string) => this._onAddElement(parentId),
            });
            this.$el.append(itemView.render().el);
        });

        this.$el.append(`
            <button class="jux-hierarchy-add-btn" data-action="add-global">
                <span class="dashicons dashicons-plus"></span> Add elements
            </button>
        `);

        if (this._selectedId) {
            this.$(`[data-id="${this._selectedId}"] .jux-hierarchy-label`).first()
                .addClass('is-selected');
        }

        return this;
    }

    private onAddFirst() {
        this._onAddElement();
    }

    private onAddGlobal() {
        this._onAddElement();
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
