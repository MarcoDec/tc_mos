import {Model} from '../modules'

export default class Collection extends Model {
    static entity = 'collections'

    get body() {
        return {...this.search, ...this.order, page: this.page}
    }

    get direction() {
        return this.asc ? 'asc' : 'desc'
    }

    get isSorted() {
        return Boolean(this.sort)
    }

    get order() {
        return this.sort === null ? {} : {[`order[${this.sortName}]`]: this.asc ? 'asc' : 'desc'}
    }

    get pages() {
        return Math.ceil(this.total / this.perPage)
    }

    static fields() {
        return {
            ...super.fields(),
            asc: this['boolean'](false),
            first: this.number(1),
            id: this.string(null),
            last: this.number(1),
            next: this.number(1),
            page: this.number(1),
            perPage: this.number(15),
            prev: this.number(1),
            search: this.attr({}),
            sort: this.string(null).nullable(),
            sortName: this.string(null).nullable(),
            total: this.number(0)
        }
    }
}
