import Backbone from 'backbone';
import type { BuilderNode } from '@/types/index.d';

/**
 * ElementNodeModel
 * Typed Backbone.Model<BuilderNode> for strict mode.
 * get/set calls are fully typed via the generic parameter.
 */
class ElementNodeModel extends Backbone.Model<BuilderNode> {
    defaults(): Partial<BuilderNode> {
        return {
            id: 'jux-' + Date.now(),
            tag: '',
            name: '',
            info: '',
            options: {},
            children: null,
        };
    }

    /** Build a WordPress shortcode string from this node */
    toShortcode(): string {
        const tag = this.get('tag') ?? '';
        const options = this.get('options') ?? {};
        const children = this.get('children');

        const atts = Object.entries(options)
            .filter(([k, v]) => k !== '_jux_id' && k !== '_label' && v !== '' && v != null)
            .map(([k, v]) => `${k}="${String(v)}"`)
            .join(' ');

        const attStr = atts ? ' ' + atts : '';

        if (children && children.length > 0) {
            const innerContent = children
                .map((child) => new ElementNodeModel(child).toShortcode())
                .join('');
            return `[${tag}${attStr}]${innerContent}[/${tag}]`;
        }

        return `[${tag}${attStr}]`;
    }

    /** Get display label: _label > name > tag */
    getDisplayName(): string {
        const options = this.get('options') ?? {};
        return (options['_label'] as string | undefined)
            ?? this.get('name')
            ?? this.get('tag')
            ?? '';
    }

    /** Deep clone for undo/redo */
    deepClone(): ElementNodeModel {
        return new ElementNodeModel(JSON.parse(JSON.stringify(this.toJSON())));
    }
}

export { ElementNodeModel };
