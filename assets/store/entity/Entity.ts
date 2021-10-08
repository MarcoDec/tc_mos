import Module from './Module'
import Property from './Property'

export default abstract class Entity extends Module {
    protected constructor(moduleName: string, properties: readonly Property[]) {
        super(moduleName, [
            new Property('id', 'number'),
            new Property('deleted', 'boolean')
        ].concat(properties))
    }
}
