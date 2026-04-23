import Backbone from 'backbone';
import _ from 'underscore';
import type { BuilderNode } from '@/types/index.d';

interface HierarchyItemOptions extends Backbone.ViewOptions {
    node: BuilderNode;
    depth: number;
    onGear: (node: BuilderNode) => void;
    onSelect: (node: BuilderNode, $label: JQuery) => void;
    onAddChild: (parentId: string) => void;
}

class HierarchyItemView extends Backbone.View<Backbone.Model, HTMLElement> {
    private node: BuilderNode;
    private depth: number;
    private _onGear: (node: BuilderNode) => void;
    private _onSelect: (node: BuilderNode, $label: JQuery) => void;
    private _onAddChild: (parentId: string) => void;
    private _collapsed: boolean = false;

    events = {
        'click .jux-hierarchy-name': 'onNameClick',
        'click .jux-hierarchy-gear': 'onGearClick',
        'click .jux-hierarchy-toggle': 'onToggle',
        'click .jux-hierarchy-add-btn': 'onAddChildClick',
    };

    constructor(options: HierarchyItemOptions) {
        super(options);
        this.node = options.node;
        this.depth = options.depth || 0;
        this._onGear = options.onGear;
        this._onSelect = options.onSelect;
        this._onAddChild = options.onAddChild;
        this.$el.attr('data-id', this.node.id);
        this.tagName = 'div';
        this.className = 'jux-hierarchy-item';
    }

    private getIconForTag(tag: string): string {
        const map: Record<string, string> = {
            'ux_banner': 'dashicons-format-image',
            'text_box': 'dashicons-editor-paragraph',
            'button': 'dashicons-button',
            'row': 'dashicons-layout',
            'section': 'dashicons-feedback',
            'ux_slider': 'dashicons-images-alt2',
            'ux_image': 'dashicons-format-image',
            'blog_posts': 'dashicons-admin-post',
            'text': 'dashicons-editor-textcolor',
            'ux_gallery': 'dashicons-format-gallery',
            'featured_box': 'dashicons-star-filled',
            'ux_image_box': 'dashicons-art',
            'team_member': 'dashicons-admin-users',
            'ux_stack': 'dashicons-list-view',
            'ux_price_table': 'dashicons-tag',
            'ux_hotspot': 'dashicons-location',
            'gap': 'dashicons-minus',
        };
        return map[tag] || 'dashicons-admin-generic';
    }

    private template = _.template(`
        <div class="jux-hierarchy-label">
            <% if (hasChildren) { %>
            <button class="jux-hierarchy-toggle dashicons dashicons-arrow-down-alt2"></button>
            <% } %>
            <span class="jux-element-icon dashicons <%- iconClass %>"></span>
            <span class="jux-hierarchy-name"><%- displayName %></span>
            <div class="jux-hierarchy-actions">
                <button class="dashicons dashicons-admin-generic jux-hierarchy-gear" title="Settings"></button>
            </div>
        </div>
    `);

    render(): this {
        const displayName = (this.node.options?._label as string | undefined)
            || this.node.name
            || this.node.tag;

        this.$el.html(this.template({
            displayName,
            iconClass: this.getIconForTag(this.node.tag),
            hasChildren: !!(this.node.children && this.node.children.length > 0),
        }));

        if (this.node.children && this.node.children.length > 0) {
            const $children = jQuery('<div class="jux-hierarchy-children">');
            this.node.children.forEach((child: BuilderNode) => {
                const childView = new HierarchyItemView({
                    node: child,
                    depth: this.depth + 1,
                    onGear: this._onGear,
                    onSelect: this._onSelect,
                    onAddChild: this._onAddChild,
                });
                $children.append(childView.render().el);
            });
            $children.append(
                `<button class="jux-hierarchy-add-btn" data-parent="${this.node.id}">
                    <span class="dashicons dashicons-plus"></span> Thêm vào ${this.node.name || this.node.tag}
                </button>`
            );
            this.$el.append($children);
        }

        return this;
    }

    private onNameClick() {
        const $label = this.$('.jux-hierarchy-label').first();
        this._onSelect(this.node, $label);
    }

    private onGearClick(e: JQuery.ClickEvent) {
        e.stopPropagation();
        this._onGear(this.node);
    }

    private onToggle(e: JQuery.ClickEvent) {
        e.stopPropagation();
        this._collapsed = !this._collapsed;
        const $children = this.$('.jux-hierarchy-children').first();
        const $btn = this.$('.jux-hierarchy-toggle').first();
        if (this._collapsed) {
            $children.hide();
            $btn.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-right-alt2');
        } else {
            $children.show();
            $btn.removeClass('dashicons-arrow-right-alt2').addClass('dashicons-arrow-down-alt2');
        }
    }

    private onAddChildClick(e: JQuery.ClickEvent) {
        const parentId = jQuery(e.currentTarget).data('parent') as string;
        if (parentId) this._onAddChild(parentId);
    }
}

export default HierarchyItemView;
