import Backbone from 'backbone';
import type { JUXBuilderData, SidebarView, BuilderNode, ElementDefinition, ElementOption } from '@/types/index.d';
import HierarchyView from '@/views/HierarchyView';
import OptionsView from '@/views/OptionsView';
import AddPanelView from '@/views/AddPanelView';
import { ElementNodeCollection } from '@/collections/ElementNodeCollection';
import { ElementNodeModel } from '@/models/ElementNode';

const MAX_HISTORY = 50;

class BuilderApp extends Backbone.View<Backbone.Model, HTMLElement> {
    private _data: JUXBuilderData;
    private _currentView: SidebarView = 'home';
    private _historyStack: string[] = [];
    private _historyIndex: number = -1;
    private _isSaving: boolean = false;
    private _pendingParentId: string | undefined;
    private _nodes: ElementNodeCollection;
    private _hierarchyView: HierarchyView;
    private _addPanelView: AddPanelView;
    private _optionsView: OptionsView | null = null;

    events = {
        'click #jux-open-add-panel': 'openAddPanel',
        'click #jux-save-post': 'savePost',
        'click #jux-exit-builder': 'exitBuilder',
        'click #jux-undo': 'undo',
        'click #jux-redo': 'redo',
        'click .jux-device-switcher button': 'switchDevice',
        'click #jux-open-settings': 'showSettings',
        'click #jux-back-from-settings': 'showHome',
        'click #jux-discard-settings': 'showHome',
        'click #jux-apply-settings': 'showHome',
        'click #jux-save-footer': 'savePost',
        'click #jux-sidebar-collapse': 'toggleSidebar',
        'click #jux-sidebar-exit': 'exitBuilder',
        'click .jux-hierarchy-add-btn': 'onHierarchyAdd',
    };

    constructor(data: JUXBuilderData) {
        super();
        this._data = data;
        // Use setElement() instead of directly assigning this.el / this.$el.
        // Backbone's super() binds delegateEvents() to a freshly-created detached
        // div; setElement() swaps the element AND re-runs delegateEvents() so that
        // all click handlers in `events` are bound to the real DOM node.
        this.setElement(document.getElementById('jux-builder-wrapper') as HTMLElement);

        this._nodes = new ElementNodeCollection();
        this._nodes.reset(data.contentNodes || []);

        this._hierarchyView = new HierarchyView({
            collection: this._nodes,
            onGear: this._configureNode.bind(this),
            onAddElement: this._openAddPanelForParent.bind(this),
        });

        this._addPanelView = new AddPanelView({
            elements: data.elements,
            onAdd: this._addElement.bind(this),
        });
        this._addPanelView.on('import', this._importShortcodes.bind(this));

        window.addEventListener('message', this._onIframeMessage.bind(this));

        this._pushHistory();
    }

    render(): this {
        this._hierarchyView.render();
        this._removeLoader();
        this._updateUndoRedoButtons();
        return this;
    }

    private _showView(view: SidebarView) {
        jQuery('.jux-sidebar-view').hide();
        jQuery(`#jux-view-${view}`).show();
        this._currentView = view;
    }

    showHome() {
        this._showView('home');
        jQuery('#jux-sidebar-title').text(this._data.postTitle || '');
    }

    showSettings() {
        this._showView('settings');
    }

    private _configureNode(node: BuilderNode) {
        const elementDef = (this._data.elements[node.tag] || {}) as ElementDefinition;

        if (this._optionsView) {
            this._optionsView.undelegateEvents();
        }

        this._optionsView = new OptionsView({
            node,
            elementDef,
            onApply: this._applyNodeOptions.bind(this),
            onDiscard: this.showHome.bind(this),
        });

        this._showView('shortcode');
        this._optionsView.render();
    }

    private _applyNodeOptions(node: BuilderNode, values: Record<string, unknown>) {
        const model = this._nodes.findById(node.id);
        if (model) {
            const merged = { ...((model.get('options') as Record<string, unknown>) ?? {}), ...values };
            model.set('options', merged);
        } else {
            this._deepUpdateNode(node.id, values);
            this._nodes.trigger('change');
        }

        this._pushHistory();
        this._updatePreview();
        this.showHome();
    }

    private _deepUpdateNode(id: string, values: Record<string, unknown>) {
        const nodes = JSON.parse(JSON.stringify(this._nodes.toJSON())) as BuilderNode[];
        this._walkAndUpdate(nodes, id, values);
        this._nodes.reset(nodes);
    }

    private _walkAndUpdate(nodes: BuilderNode[], id: string, values: Record<string, unknown>): boolean {
        for (const node of nodes) {
            if (node.id === id) {
                node.options = { ...node.options, ...values };
                return true;
            }
            if (node.children) {
                if (this._walkAndUpdate(node.children, id, values)) return true;
            }
        }
        return false;
    }

    openAddPanel() {
        this._openAddPanelForParent(undefined);
    }

    private _openAddPanelForParent(parentId?: string) {
        this._pendingParentId = parentId;
        this._addPanelView.open();
    }

    private onHierarchyAdd(e: JQuery.ClickEvent) {
        const parentId = jQuery(e.currentTarget).data('parent') as string | undefined;
        this._openAddPanelForParent(parentId);
    }

    private _addElement(tag: string) {
        const el = this._data.elements[tag] as ElementDefinition | undefined;
        if (!el) return;

        const defaultOptions: Record<string, unknown> = {};
        Object.entries(el.options ?? {}).forEach(([k, opt]: [string, ElementOption]) => {
            if (opt.default !== undefined) defaultOptions[k] = opt.default;
        });

        const nodeData: BuilderNode = {
            id: 'jux-' + Date.now() + '-' + Math.random().toString(36).slice(2, 7),
            tag,
            name: el.name || tag,
            info: el.description || '',
            options: defaultOptions,
            children: el.wrap || el.type === 'container' ? [] : null,
        };

        if (this._pendingParentId) {
            this._addChildNode(this._pendingParentId, nodeData);
            this._pendingParentId = undefined;
        } else {
            this._nodes.add(new ElementNodeModel(nodeData));
        }

        this._pushHistory();
        this._updatePreview();
    }

    private _addChildNode(parentId: string, childData: BuilderNode) {
        const nodes = JSON.parse(JSON.stringify(this._nodes.toJSON())) as BuilderNode[];
        this._walkAndAddChild(nodes, parentId, childData);
        this._nodes.reset(nodes);
    }

    private _walkAndAddChild(nodes: BuilderNode[], parentId: string, child: BuilderNode): boolean {
        for (const node of nodes) {
            if (node.id === parentId) {
                node.children = node.children || [];
                node.children.push(child);
                return true;
            }
            if (node.children && this._walkAndAddChild(node.children, parentId, child)) return true;
        }
        return false;
    }

    private _importShortcodes(raw: string) {
        jQuery.post(this._data.ajaxUrl, {
            action: 'jux_builder_parse_shortcodes',
            nonce: this._data.nonce,
            content: raw,
        }, (response: { success: boolean; data?: { nodes: BuilderNode[] } }) => {
            if (response.success && response.data?.nodes) {
                this._nodes.reset(response.data.nodes);
                this._pushHistory();
                this._updatePreview();
            }
        });
    }

    savePost() {
        if (this._isSaving) return;
        this._isSaving = true;

        const $btn = jQuery('#jux-save-footer');
        const $topBtn = jQuery('#jux-save-post');
        const originalText = $topBtn.text();
        const footerLabel = this._data.postStatus === 'publish' ? 'Update' : 'Save Draft';

        $btn.text('Saving…');
        $topBtn.text('Saving…').prop('disabled', true);

        const content = this._nodes.toShortcodeContent();

        jQuery.post(this._data.ajaxUrl, {
            action: 'jux_save_content',
            post_id: this._data.postId,
            security: this._data.nonce,
            content,
            status: this._data.postStatus === 'publish' ? 'publish' : 'draft',
        })
            .done((response: { success: boolean }) => {
                const label = this._data.postStatus === 'publish' ? '✓ Updated' : '✓ Saved';
                $btn.text(response.success ? label : '✗ Error');
                $topBtn.text(response.success ? label : '✗ Error');

                setTimeout(() => {
                    $btn.text(footerLabel);
                    $topBtn.text(originalText).prop('disabled', false);
                }, 2000);
            })
            .fail(() => {
                $btn.text('✗ Failed');
                $topBtn.text('✗ Failed');
                setTimeout(() => {
                    $btn.text(footerLabel);
                    $topBtn.text(originalText).prop('disabled', false);
                }, 2000);
            })
            .always(() => {
                this._isSaving = false;
            });
    }

    exitBuilder() {
        if (confirm('Exit builder? Unsaved changes will be lost.')) {
            window.location.href = this._data.backUrl || '/wp-admin/';
        }
    }

    private switchDevice(e: JQuery.ClickEvent) {
        const device = jQuery(e.currentTarget).data('device') as string;
        jQuery('.jux-device-switcher button').removeClass('active');
        jQuery(e.currentTarget).addClass('active');
        jQuery('#jux-preview-container')
            .removeClass('desktop tablet mobile')
            .addClass(device);
    }

    private toggleSidebar() {
        const $sidebar = jQuery('#jux-sidebar');
        const isCollapsed = $sidebar.toggleClass('collapsed').hasClass('collapsed');
        const $icon = jQuery('#jux-sidebar-collapse .dashicons');
        $icon
            .toggleClass('dashicons-arrow-left-alt2', !isCollapsed)
            .toggleClass('dashicons-arrow-right-alt2', isCollapsed);
    }

    private _pushHistory() {
        const snapshot = JSON.stringify(this._nodes.toJSON());
        this._historyStack = this._historyStack.slice(0, this._historyIndex + 1);
        this._historyStack.push(snapshot);
        if (this._historyStack.length > MAX_HISTORY) {
            this._historyStack.shift();
        }
        this._historyIndex = this._historyStack.length - 1;
        this._updateUndoRedoButtons();
    }

    undo() {
        if (this._historyIndex <= 0) return;
        this._historyIndex--;
        this._restoreFromHistory();
    }

    redo() {
        if (this._historyIndex >= this._historyStack.length - 1) return;
        this._historyIndex++;
        this._restoreFromHistory();
    }

    private _restoreFromHistory() {
        const snapshot = JSON.parse(this._historyStack[this._historyIndex]) as BuilderNode[];
        this._nodes.reset(snapshot);
        this._updateUndoRedoButtons();
        this._updatePreview();
    }

    private _updateUndoRedoButtons() {
        jQuery('#jux-undo').prop('disabled', this._historyIndex <= 0);
        jQuery('#jux-redo').prop('disabled', this._historyIndex >= this._historyStack.length - 1);
    }

    private _updatePreview() {
        const nodes = this._nodes.toJSON() as BuilderNode[];

        const shortcodes = nodes.map((node) => ({
            id: node.id,
            tag: node.tag,
            atts: node.options ?? {},
            content: node.children ? this._buildContentFromChildren(node.children) : '',
        }));

        jQuery.ajax({
            url: this._data.ajaxUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'jux_builder_render_preview',
                nonce: this._data.nonce,
                shortcodes,
            },
        })
            .done((response: { success: boolean; data?: { rendered: Array<{ id: string; html: string }> } }) => {
                if (response.success && response.data?.rendered) {
                    this._sendToIframe('jux-rendered', { items: response.data.rendered });
                }
            })
            .fail((xhr: JQueryXHR) => {
                console.error('[JUX] Preview AJAX failed:', xhr.status, xhr.statusText);
            });
    }

    private _buildContentFromChildren(children: BuilderNode[]): string {
        return children
            .map((child) => {
                const m = new ElementNodeModel(child);
                return m.toShortcode();
            })
            .join('');
    }

    private _sendToIframe(action: string, payload: Record<string, unknown>) {
        const frame = document.getElementById('jux-preview-frame') as HTMLIFrameElement | null;
        frame?.contentWindow?.postMessage({ action, ...payload }, '*');
    }

    private _onIframeMessage(e: MessageEvent) {
        if (!e.data?.action) return;
        const msg = e.data as { action: string; id?: string };
        switch (msg.action) {
            case 'jux-element-click': {
                const model = this._nodes.findById(msg.id ?? '');
                if (model) this._configureNode(model.toJSON() as BuilderNode);
                break;
            }
            case 'jux-ready':
                this._updatePreview();
                break;
        }
    }

    private _removeLoader() {
        const $wrapper = this.$el;
        const $iframe = jQuery('#jux-preview-frame');
        $iframe.on('load', () => setTimeout(() => $wrapper.removeClass('jux-loading'), 300));
        setTimeout(() => $wrapper.removeClass('jux-loading'), 5000);
    }
}

export default BuilderApp;
