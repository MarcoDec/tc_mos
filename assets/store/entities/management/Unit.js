import {Entity} from '../../modules'

export default class Unit extends Entity {
    static entity = 'units'

    static fields() {
        return {
            ...super.fields(),
            base: this.number(1),
            code: this.string(null).nullable(),
            name: this.string(null).nullable(),
            parent: this.string(null).nullable()
        }
    }
}
