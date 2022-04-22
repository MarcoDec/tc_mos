import {Entity, Node} from '../modules'

export default class Family extends Entity {
    static entity = 'families'

    get fullName() {
        return this.parent ? `${this.parent.fullName}\\${this.name}` : this.name
    }

    get label() {
        return this.name
    }

    get option() {
        return {text: this.fullName, value: this['@id']}
    }

    static fields() {
        return {
            ...super.fields(),
            code: this.string(null).nullable(),
            copperable: this['boolean'](false),
            customsCode: this.string(null).nullable(),
            name: this.string(null).nullable(),
            node: this.belongsTo(Node, 'nodeId'),
            nodeId: this.string(null).nullable(),
            parentId: this.number(0)
        }
    }
}
