import {ComponentFamily, Model, ProductFamily} from '../modules'

export default class Node extends Model {
    static entity = 'tree-nodes'

    get children() {
        return (this.relation?.children ?? []).map(child => child.node)
    }

    get hasChildren() {
        return this.children.length > 0
    }

    get hasParent() {
        return this.hasRelation && Boolean(this.parent)
    }

    get hasRelation() {
        return Boolean(this.relation)
    }

    get label() {
        return this.relation?.label ?? null
    }

    get parent() {
        return this.relation?.parent?.node ?? null
    }

    get relation() {
        return this.componentFamily ?? this.productFamily
    }

    static fields() {
        return {
            ...super.fields(),
            componentFamily: this.hasOne(ComponentFamily, 'nodeId'),
            id: this.string(null),
            opened: this['boolean'](false),
            productFamily: this.hasOne(ProductFamily, 'nodeId'),
            selected: this['boolean'](false)
        }
    }

    hasThisParent(node) {
        return this.hasParent ? this.parent.id === node.id || this.parent.hasThisParent(node) : false
    }
}
