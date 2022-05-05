import {Entity} from '../../modules'

export default class Incoterms extends Entity {
    static entity = 'incoterms'
    roleAdmin = 'isLogisticsAdmin'
    roleWriter = 'isLogisticsAdmin'

    get option() {
        return {text: this.code, value: this['@id']}
    }

    static fields() {
        return {
            ...super.fields(),
            code: this.string(null),
            name: this.string(null)
        }
    }
}
