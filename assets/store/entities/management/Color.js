import {Entity} from '../../modules'

export default class Color extends Entity {
    static entity = 'colors'

    static fields() {
        return {
            ...super.fields(),
            name: this.string(null),
            rgb: this.string(null)
        }
    }
}
