import {Entity} from '../../modules'

export default class VatMessage extends Entity {
    static entity = 'vat-messages'
    roleAdmin = 'isManagementAdmin'
    roleWriter = 'isManagementAdmin'

    get option() {
        return {text: this.name, value: this['@id']}
    }

    static fields() {
        return {
            ...super.fields(),
            name: this.string(null).nullable()
        }
    }
}
