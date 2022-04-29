import {Entity} from '../../modules'

export default class QualityType extends Entity {
    static entity = 'quality-types'
    roleAdmin = 'isQualityAdmin'
    roleWriter = 'isQualityAdmin'

    static fields() {
        return {
            ...super.fields(),
            name: this.string(null)
        }
    }
}
