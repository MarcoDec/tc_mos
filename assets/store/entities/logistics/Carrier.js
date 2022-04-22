import {Address, Entity} from '../../modules'

export default class Carrier extends Entity {
    static entity = 'carriers'

    static fields() {
        return {
            ...super.fields(),
            address: this.hasOne(Address, 'carrierId'),
            name: this.string(null).nullable()
        }
    }
}
