import {Entity} from '../../../modules'

export default class RejectType extends Entity {
    static entity = 'reject-types'

    static fields() {
        return {
            ...super.fields(),
            name: this.string(null)
        }
    }
}
