import {Entity} from '../../modules'

export default class Address extends Entity {
    static entity = 'addresses'

    static fields() {
        return {
            ...super.fields(),
            address: this.string(null).nullable(),
            address2: this.string(null).nullable(),
            carrierId: this.number(0),
            city: this.string(null).nullable(),
            country: this.string(null).nullable(),
            email: this.string(null).nullable(),
            id: this.string(null),
            outTrainerId: this.number(0),
            phoneNumber: this.string(null).nullable(),
            zipCode: this.string(null).nullable()
        }
    }
}
