import {Model} from '@vuex-orm/core'

export default class Color extends Model {
    static entity = 'colors'
    static primaryKey = 'id'

    static fields() {
        return {
            '@context': this.string(null),
            '@id': this.string(null),
            '@type': this.string(null),
            id: this.number(0),
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
