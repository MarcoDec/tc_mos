import {Model} from '../modules'

export default class Entity extends Model {
    static entity = 'entities'

    static fields() {
        return {
            ...super.fields(),
            '@context': this.string(null),
            '@id': this.string(null),
            '@type': this.string(null)
        }
    }
}
