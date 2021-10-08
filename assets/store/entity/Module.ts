import type Property from './Property'
import type {State} from './Property'

export default abstract class Module {
    protected constructor(private readonly properties: readonly Property[]) {
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
