import Backbone from 'backbone';
import _ from 'underscore';
import type { ElementDefinition, ElementOption, BuilderNode } from '@/types/index.d';

interface OptionsViewOptions extends Backbone.ViewOptions {
    node: BuilderNode;
    elementDef: ElementDefinition;
    onApply: (node: BuilderNode, values: Record<string, unknown>) => void;
    onDiscard: () => void;
}

class OptionsView extends Backbone.View<Backbone.Model, HTMLElement> {
    private _node: BuilderNode;
    private _elementDef: ElementDefinition;
    private _onApply: (node: BuilderNode, values: Record<string, unknown>) => void;
    private _onDiscard: () => void;

    events = {
        'click #jux-apply-shortcode': 'onApply',
        'click #jux-discard-shortcode': 'onDiscard',
        'click #jux-back-from-shortcode': 'onDiscard',
        'input input, change select, change textarea': 'onFieldChange',
    };

    constructor(options: OptionsViewOptions) {
        super(options);
        this._node = options.node;
        this._elementDef = options.elementDef;
        this._onApply = options.onApply;
        this._onDiscard = options.onDiscard;
        this.el = document.getElementById('jux-view-shortcode') as HTMLElement;
        this.$el = jQuery(this.el);
    }

    renderField(key: string, opt: ElementOption, currentValue: unknown): string {
        const val = currentValue !== undefined ? currentValue : (opt.default ?? '');
        const name = `jux-opt-${key}`;

        switch (opt.type) {
            case 'textarea':
                return `
                    <textarea name="${name}" rows="4"
                        placeholder="${_.escape(opt.placeholder ?? '')}"
                        data-key="${key}"
                    >${_.escape(String(val))}</textarea>
                `;

            case 'select': {
                const opts = Object.entries(opt.options ?? {})
                    .map(([v, label]) => {
                        const text = typeof label === 'object' ? label.title : label;
                        const selected = v === String(val) ? 'selected' : '';
                        return `<option value="${_.escape(v)}" ${selected}>${_.escape(text)}</option>`;
                    })
                    .join('');
                return `<select name="${name}" data-key="${key}">${opts}</select>`;
            }

            case 'radio-buttons': {
                const btns = Object.entries(opt.options ?? {})
                    .map(([v, data]) => {
                        const title = typeof data === 'object' ? data.title : data;
                        const checked = v === String(val) ? 'checked' : '';
                        return `
                            <label>
                                <input type="radio" name="${name}" value="${_.escape(v)}" ${checked}
                                    data-key="${key}">
                                ${_.escape(title)}
                            </label>
                        `;
                    })
                    .join('');
                return `<div class="jux-radio-buttons">${btns}</div>`;
            }

            case 'checkbox': {
                const checked = val === 'yes' || val === true ? 'checked' : '';
                return `
                    <label>
                        <input type="checkbox" name="${name}" value="yes" ${checked}
                            data-key="${key}">
                        ${_.escape(opt.heading ?? '')}
                    </label>
                `;
            }

            case 'color':
                return `<input type="color" name="${name}" value="${_.escape(String(val))}"
                    data-key="${key}">`;

            case 'number':
            case 'scrubfield':
                return `<input type="number" name="${name}" value="${_.escape(String(val))}"
                    placeholder="${_.escape(opt.placeholder ?? '')}" data-key="${key}">`;

            default: // textfield
                return `<input type="text" name="${name}" value="${_.escape(String(val))}"
                    placeholder="${_.escape(opt.placeholder ?? '')}" data-key="${key}">`;
        }
    }

    render(): this {
        const defName = this._elementDef?.title || this._node.name || this._node.tag;
        this.$('#jux-shortcode-title').text(defName);

        const $body = this.$('#jux-shortcode-options').empty();
        const opts = this._elementDef?.options ?? {};

        if (Object.keys(opts).length === 0) {
            $body.html('<p style="font-size:12px;color:#999;padding:10px">No options for this element.</p>');
            return this;
        }

        Object.entries(opts).forEach(([key, opt]) => {
            const currentValue = (this._node.options ?? {})[key];
            const fieldHtml = this.renderField(key, opt, currentValue);

            $body.append(`
                <div class="jux-option-group" data-key="${key}">
                    <label class="jux-option-label">${_.escape(opt.heading ?? key)}</label>
                    <div class="jux-option-field">${fieldHtml}</div>
                </div>
            `);
        });

        this.$el.show();
        return this;
    }

    collectValues(): Record<string, unknown> {
        const values: Record<string, unknown> = {};

        this.$('#jux-shortcode-options [data-key]').each((_idx, el) => {
            const $el = jQuery(el);
            const key = $el.attr('data-key') as string;
            const type = ($el.attr('type') ?? 'text').toLowerCase();

            if (type === 'checkbox') {
                values[key] = $el.is(':checked') ? 'yes' : 'no';
            } else if (type === 'radio') {
                if ($el.is(':checked')) values[key] = $el.val();
            } else {
                values[key] = $el.val();
            }
        });

        return values;
    }

    private onFieldChange() { }

    private onApply() {
        const values = this.collectValues();
        this._onApply(this._node, values);
    }

    private onDiscard() {
        this._onDiscard();
    }
}

export default OptionsView;
