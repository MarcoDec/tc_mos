import {Entity} from '../../../modules'

export default class Society extends Entity {
    static entity = 'societies'
    roleAdmin = 'isManagementAdmin'
    roleWriter = 'isManagementWriter'

    static fields() {
        return {
            ...super.fields(),
            name: this.string(null).nullable()
        }
    }
}
