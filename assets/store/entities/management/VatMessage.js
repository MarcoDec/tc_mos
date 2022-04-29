import {Entity} from '../../modules'

export default class VatMessage extends Entity {
    static entity = 'vat-messages'
    roleAdmin = 'isManagementAdmin'
    roleWriter = 'isManagementAdmin'

    static fields() {
        return {
            ...super.fields(),
            name: this.string(null).nullable()
        }
    }
}
