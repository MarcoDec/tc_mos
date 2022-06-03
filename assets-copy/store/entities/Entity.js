import {EmployeeRepository, Model} from '../modules'
import {get} from 'lodash'
import store from '..'

export default class Entity extends Model {
    static entity = 'entities'
    roleAdmin = null
    roleWriter = null

    static fields() {
        return {
            ...super.fields(),
            '@context': this.string(null).nullable(),
            '@id': this.string(null).nullable(),
            '@type': this.string(null).nullable()
        }
    }

    tableItem(fields) {
        const user = store.$repo(EmployeeRepository).user
        const item = {delete: user[this.roleAdmin], id: this.id, update: user[this.roleWriter]}
        for (const field of fields)
            item[field.name] = get(this, field.name)
        return item
    }
}
