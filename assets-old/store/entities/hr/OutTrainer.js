import {Address, Entity} from '../../modules'

export default class OutTrainer extends Entity {
    static entity = 'out-trainers'
    roleAdmin = 'isHrAdmin'
    roleWriter = 'isHrWriter'

    get countryLabel() {
        return this.address?.countryLabel ?? null
    }

    static fields() {
        return {
            ...super.fields(),
            address: this.hasOne(Address, 'outTrainerId'),
            name: this.string(null).nullable(),
            surname: this.string(null).nullable()
        }
    }

    tableItem(fields) {
        const item = super.tableItem(fields)
        item['address.countryLabel'] = this.countryLabel
        return item
    }
}
