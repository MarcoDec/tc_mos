import {Entity} from '../../../modules'

export default class EventType extends Entity {
    static entity = 'event-types'
    roleAdmin = 'isHrAdmin'
    roleWriter = 'isHrAdmin'

    static fields() {
        return {
            ...super.fields(),
            name: this.string(null)
        }
    }
}
