import {Unit} from '../../modules'

export default class Currency extends Unit {
    static entity = 'currencies'

    static fields() {
        return {
            ...super.fields(),
            active: this['boolean'](false),
            parentInstance: this.belongsTo(Currency, 'parentId'),
            symbol: this.string(null).nullable()
        }
    }
}
