import type Property from './Property'
import type {State} from './Property'
import {useStore} from 'vuex'

export default abstract class Module {
    protected constructor(
        public readonly moduleName: string,
        private readonly properties: readonly Property[]
    ) {
        for (const property of this.properties) {
            Object.defineProperty(this, property.name, {
                get: () => {
                    const store = useStore()
                    return store.hasModule(this.moduleName)
                        ? store.state[`${this.moduleName}/${property.name}`]
                        : undefined
                }
            })
        }
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
