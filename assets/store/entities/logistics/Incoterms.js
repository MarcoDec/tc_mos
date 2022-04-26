import {Entity} from '../../modules'

export default class Incoterms extends Entity {
    static entity = 'incoterms'

    static fields() {
        return {
            ...super.fields(),
            code: this.string(null),
            name: this.string(null)
        }
    }
}
