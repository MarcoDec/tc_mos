import {ENGINE_GROUPS} from '../../../../utils'
import {Entity} from '../../../modules'

export default class Group extends Entity {
    static entity = 'engine-groups'
    roleAdmin = 'isProductionAdmin'
    roleWriter = 'isProductionAdmin'

    get '@typeLabel'() {
        return ENGINE_GROUPS[this['@type']]
    }

    static fields() {
        return {
            ...super.fields(),
            code: this.string(null),
            name: this.string(null)
        }
    }

    tableItem(fields) {
        const item = super.tableItem(fields)
        item['@typeLabel'] = this['@typeLabel']
        return item
    }
}
