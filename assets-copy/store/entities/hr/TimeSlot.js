import {Entity} from '../../modules'

export default class TimeSlot extends Entity {
    static entity = 'time-slots'
    roleAdmin = 'isHrAdmin'
    roleWriter = 'isHrAdmin'

    static fields() {
        return {
            ...super.fields(),
            end: this.string(null).nullable(),
            endBreak: this.string(null).nullable(),
            name: this.string(null).nullable(),
            start: this.string(null).nullable(),
            startBreak: this.string(null).nullable()
        }
    }
}
