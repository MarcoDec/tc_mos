import {Model} from '@vuex-orm/core'

export default class Color extends Model {
    static entity = 'colors'
    static primaryKey = 'id'

    static fields() {
        return {
            '@context': this.string(),
            '@id': this.string(),
            '@type': this.string(),
            id: this.number(),
            name: this.string(),
            rgb: this.string()
        }
    }

    tableItem(fields) {
        const item = {delete: true, update: true}
        for (const field of fields)
            item[field.name] = this[field.name]
        return item
    }
}
