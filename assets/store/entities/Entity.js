import {Model} from '../modules'

export default class Entity extends Model {
    static entity = 'entities'

    static fields() {
        return {
            ...super.fields(),
            '@context': this.string(null),
            '@id': this.string(null),
            '@type': this.string(null)
        }
    }

    tableItem(fields) {
        const item = {delete: true, id: this.id, update: true}
        for (const field of fields)
            item[field.name] = this[field.name]
        return item
    }
}
