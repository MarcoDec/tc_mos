import {Address, Entity} from '../../modules'

export default class OutTrainer extends Entity {
    static entity = 'out-trainers'

    static fields() {
        return {
            ...super.fields(),
            address: this.hasOne(Address, 'outTrainerId'),
            name: this.string(null).nullable(),
            surname: this.string(null).nullable()
        }
    }
}
