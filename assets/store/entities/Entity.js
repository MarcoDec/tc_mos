import {Model} from '../modules'
import {get} from 'lodash'

export default class Entity extends Model {
    static entity = 'entities'

    static fields() {
        return {
            ...super.fields(),
            '@context': this.string(null).nullable(),
            '@id': this.string(null).nullable(),
            '@type': this.string(null).nullable()
        }
    }

    tableItem(fields) {
        const item = {delete: true, id: this.id, update: true}
        for (const field of fields)
            item[field.name] = get(this, field.name)
        return item
    }
}
