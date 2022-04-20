import {Model} from '../modules'

export default class Collection extends Model {
    static entity = 'collections'

    get pages() {
        return Math.ceil(this.total / this.perPage)
    }

    static fields() {
        return {
            ...super.fields(),
            first: this.number(1),
            id: this.string(null),
            last: this.number(1),
            next: this.number(1),
            page: this.number(1),
            perPage: this.number(15),
            prev: this.number(1),
            total: this.number(0)
        }
    }
}
