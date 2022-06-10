import {Model} from '@vuex-orm/core'

export default class Country extends Model {
    static entity = 'countries'
    static primaryKey = 'code'

    get option() {
        return {text: this.name, value: this.code}
    }

    static fields() {
        return {
            ...super.fields(),
            code: this.string(null),
            group: this.string(null),
            name: this.string(null)
        }
    }
}
