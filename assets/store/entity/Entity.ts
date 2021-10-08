import Module from './Module'
import Property from './Property'

export default abstract class Entity extends Module {
    protected constructor(properties: readonly Property[]) {
        super([
            new Property('id', 'number'),
            new Property('deleted', 'boolean')
        ].concat(properties))
    }
}
