import Module from './Module'
import Property from './Property'

export default abstract class Entity extends Module {
    protected constructor(module: string, properties: readonly Property[]) {
        super(module, [
            new Property('id', 'number'),
            new Property('deleted', 'boolean')
        ].concat(properties))
    }
}
