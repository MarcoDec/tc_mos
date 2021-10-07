import Property from './Property'
import type {State} from './Property'

export default abstract class Entity {
    private readonly properties: readonly Property[]

    protected constructor(properties: readonly Property[] = []) {
        this.properties = [
            new Property('id', 'number'),
            new Property('deleted', 'boolean')
        ].concat(properties)
    }

    public get state() {
        return (): State => {
            const state: State = {}
            for (const property of this.properties)
                state[property.name] = property.defaultValue
            return state
        }
    }
}
