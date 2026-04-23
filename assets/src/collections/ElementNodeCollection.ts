import Backbone from 'backbone';
import { ElementNodeModel } from '@/models/ElementNode';
import type { BuilderNode } from '@/types/index.d';

/**
 * ElementNodeCollection
 * Collection for typed ElementNodeModel.
 */
class ElementNodeCollection extends Backbone.Collection<ElementNodeModel> {
    model = ElementNodeModel;

    /** Serialize entire collection to WP shortcode string */
    toShortcodeContent(): string {
        return this.map((model) => model.toShortcode()).join('');
    }

    /** Find a node by id (recursive, searches children too) */
    findById(id: string): ElementNodeModel | null {
        for (const model of this.models) {
            if (model.get('id') === id) return model;

            const children = model.get('children');
            if (children) {
                const found = this._searchChildren(children, id);
                if (found) return found;
            }
        }
        return null;
    }

    private _searchChildren(
        children: BuilderNode[],
        id: string
    ): ElementNodeModel | null {
        for (const child of children) {
            if (child.id === id) {
                return new ElementNodeModel(child);
            }
            if (child.children) {
                const found = this._searchChildren(child.children, id);
                if (found) return found;
            }
        }
        return null;
    }

    /** Snapshot entire state for undo/redo */
    snapshot(): BuilderNode[] {
        return JSON.parse(JSON.stringify(this.toJSON()));
    }
}

export { ElementNodeCollection };
