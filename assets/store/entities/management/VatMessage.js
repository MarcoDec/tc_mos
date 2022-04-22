import {Entity} from '../../modules'

export default class VatMessage extends Entity {
    static entity = 'vat-messages'

    static fields() {
        return {
            ...super.fields(),
            name: this.string(null)
        }
    }
}
