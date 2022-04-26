import {Entity} from '../../modules'

export default class InvoiceTimeDue extends Entity {
    static entity = 'invoice-time-dues'

    static fields() {
        return {
            ...super.fields(),
            days: this.number(0),
            daysAfterEndOfMonth: this.number(0),
            endOfMonth: this['boolean'](false),
            name: this.string(null).nullable()
        }
    }
}
