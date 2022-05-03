import {AbstractFamily} from '../../../modules'

export default class Family extends AbstractFamily {
    static entity = 'component-families'

    static fields() {
        return {
            ...super.fields(),
            children: this.hasMany(Family, 'parentId'),
            parent: this.belongsTo(Family, 'parentId')
        }
    }
}
