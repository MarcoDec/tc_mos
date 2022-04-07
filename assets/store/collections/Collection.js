import {Model} from '@vuex-orm/core'

export default class Collection extends Model {
    static entity = 'collections'
    static primaryKey = 'name'

    static fields() {
        return {
            current: this.number(1),
            first: this.number(1),
            last: this.number(1),
            name: this.string(null),
            next: this.number(1),
            prev: this.number(1),
            total: this.number(0)
        }
    }
}
