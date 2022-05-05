import {Address, Entity, Incoterms, InvoiceTimeDue, VatMessage} from '../../../modules'

export default class Society extends Entity {
    static entity = 'societies'
    roleAdmin = 'isManagementAdmin'
    roleWriter = 'isManagementWriter'

    static fields() {
        return {
            ...super.fields(),
            accountingAccount: this.string(null).nullable(),
            address: this.hasOne(Address, 'societyId'),
            ar: this['boolean'](false),
            bankDetails: this.string(null).nullable(),
            fax: this.string(null).nullable(),
            forceVat: this.string(null).nullable(),
            incotermsId: this.number(0),
            incotermsInstance: this.belongsTo(Incoterms, 'incotermsId'),
            invoiceTimeDueId: this.number(0),
            invoiceTimeDueInstance: this.belongsTo(InvoiceTimeDue, 'invoiceTimeDueId'),
            legalForm: this.string(null).nullable(),
            name: this.string(null).nullable(),
            notes: this.string(null).nullable(),
            ppmRate: this.number(0),
            siren: this.string(null).nullable(),
            vat: this.string(null).nullable(),
            vatMessageId: this.number(0),
            vatMessageInstance: this.belongsTo(VatMessage, 'vatMessageId'),
            web: this.string(null).nullable()
        }
    }
}
