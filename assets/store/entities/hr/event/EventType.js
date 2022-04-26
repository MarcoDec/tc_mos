import {Entity} from '../../../modules'

export default class EventType extends Entity {
    static entity = 'event-types'

    static fields() {
        return {
            ...super.fields(),
            name: this.string(null)
        }
    }
}
