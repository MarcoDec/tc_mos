import {Entity} from '../../../modules'

export default class Notification extends Entity {
    static entity = 'notifications'

    static fields() {
        return {
            ...super.fields(),
            category: this.string(null),
            createdAt: this.string(null),
            read: this['boolean'](false),
            subject: this.string(null)
        }
    }
}
