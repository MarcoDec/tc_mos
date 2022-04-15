import {Entity} from '../../modules'

export default class Color extends Entity {
    static entity = 'colors'

    static fields() {
        return {
            ...super.fields(),
            name: this.string(null),
            rgb: this.string(null)
        }
    }

    tableItem(fields) {
        const item = {delete: true, id: this.id, update: true}
        for (const field of fields)
            item[field.name] = this[field.name]
        return item
    }
}
