import {Entity} from '../../modules'

export default class Unit extends Entity {
    static entity = 'units'

    get option() {
        return {text: this.code, value: this['@id']}
    }

    get optionId() {
        return {text: this.code, value: this.id}
    }

    get parentLabel() {
        return this.parentInstance?.code ?? null
    }

    static fields() {
        return {
            ...super.fields(),
            base: this.number(1),
            code: this.string(null).nullable(),
            name: this.string(null).nullable(),
            parent: this.string(null).nullable(),
            parentId: this.number(0),
            parentInstance: this.belongsTo(Unit, 'parentId')
        }
    }

    tableItem(fields) {
        const item = super.tableItem(fields)
        item.parentLabel = this.parentLabel
        return item
    }
}
