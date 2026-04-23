import Backbone from 'backbone';
import type { ElementDefinition } from '@/types/index.d';

interface AddPanelViewOptions extends Backbone.ViewOptions {
    elements: Record<string, ElementDefinition>;
    onAdd: (tag: string) => void;
}

class AddPanelView extends Backbone.View<Backbone.Model, HTMLElement> {
    private _elements: Record<string, ElementDefinition>;
    private _onAdd: (tag: string) => void;

    events = {
        'click #jux-stack-close': 'close',
        'click #jux-stack-backdrop': 'close',
        'click .add-shortcode-box-button': 'onElementClick',
        'input #jux-filter-elements': 'onFilter',
        'click .add-shortcode-types button': 'onTabSwitch',
        'click #jux-import-submit': 'onImportSubmit',
    };

    constructor(options: AddPanelViewOptions) {
        super(options);
        this._elements = options.elements;
        this._onAdd = options.onAdd;

        const el = document.getElementById('jux-app-stack') as HTMLElement;
        this.setElement(el);

        // Global delegate for stability
        jQuery(document).on('click', '#jux-stack-close', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.close();
        });

        jQuery(document).on('click', '#jux-stack-backdrop', (e) => {
            if (e.target.id === 'jux-stack-backdrop') {
                this.close();
            }
        });
    }

    getCategorized(): Record<string, Array<[string, ElementDefinition]>> {
        const result: Record<string, Array<[string, ElementDefinition]>> = {};
        Object.entries(this._elements).forEach(([tag, el]) => {
            const cat = el.category || 'General';
            if (!result[cat]) result[cat] = [];
            result[cat].push([tag, el]);
        });

        // Ensure 'Layout' is at the top
        const sorted: Record<string, Array<[string, ElementDefinition]>> = {};
        if (result['Layout']) {
            sorted['Layout'] = result['Layout'];
            delete result['Layout'];
        }
        Object.assign(sorted, result);
        return sorted;
    }

    getIconForTag(tag: string): string {
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

    renderElements() {
        const $list = this.$('#jux-panel-elements-list').empty();
        const categorized = this.getCategorized();

        if (Object.keys(categorized).length === 0) {
            $list.html('<div class="jux-no-elements"><p>No elements registered.</p></div>');
            return;
        }

        Object.entries(categorized).forEach(([catName, items]) => {
            const itemsHtml = items.map(([tag, el]) => {
                const iconClass = el.icon || this.getIconForTag(tag);
                return `
                    <li class="add-shortcode-box">
                        <button class="add-shortcode-box-button" type="button" data-tag="${tag}">
                            <div class="add-shortcode-icon-frame">
                                ${el.thumbnail ? `<img src="${el.thumbnail}" alt="${el.name}">` : `<span class="dashicons ${iconClass}"></span>`}
                            </div>
                            <span class="title">${el.name || tag}</span>
                        </button>
                    </li>
                `;
            }).join('');

            $list.append(`
                <div class="add-shortcode-category">
                    <h3>${catName}</h3>
                    <ul>${itemsHtml}</ul>
                </div>
            `);
        });
    }

    open() {
        this.renderElements();
        this.$('#jux-filter-elements').val('');
        this.$el.addClass('is-visible');
        this.$('#jux-filter-elements').trigger('focus');
    }

    close() {
        this.$el.removeClass('is-visible');
    }

    private onElementClick(e: JQuery.ClickEvent) {
        const tag = jQuery(e.currentTarget).data('tag') as string;
        if (tag) {
            this._onAdd(tag);
            this.close();
        }
    }

    private onFilter(e: JQuery.TriggeredEvent) {
        const q = (jQuery(e.currentTarget).val() as string).toLowerCase().trim();
        this.$('.add-shortcode-box').each((_idx, el) => {
            const name = jQuery(el).find('.title').text().toLowerCase();
            jQuery(el).toggle(!q || name.includes(q));
        });
        this.$('.add-shortcode-category').each((_idx, el) => {
            const anyVisible = jQuery(el).find('.add-shortcode-box:visible').length > 0;
            jQuery(el).toggle(anyVisible);
        });
    }

    private onTabSwitch(e: JQuery.ClickEvent) {
        const panel = jQuery(e.currentTarget).data('panel') as string;
        this.$('.add-shortcode-types button').removeClass('active');
        jQuery(e.currentTarget).addClass('active');
        this.$('.add-panel-body').hide();
        this.$(`#add-panel-${panel}`).show();
    }

    private onImportSubmit() {
        const raw = (this.$('#jux-import-content').val() as string).trim();
        if (raw) {
            this.trigger('import', raw);
        }
        this.close();
    }
}

export default AddPanelView;
