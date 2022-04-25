import {Entity} from '../../../modules'

export default class Group extends Entity {
    static entity = 'engine-groups'

    static fields() {
        return {
            ...super.fields(),
            code: this.string(null),
            name: this.string(null)
        }
    }
}
