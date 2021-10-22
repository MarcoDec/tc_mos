import 'reflect-metadata'
import {useNamespacedMutations, useNamespacedState} from 'vuex-composition-helpers'
import store from '../store'

export function Column(): ReturnType<typeof Reflect.metadata> {
    return Reflect.metadata('type', 'column')
}

type ModuleState = {
    namespace: string
    vueComponents: string[]
}

export default abstract class Module<T = unknown> {
    @Column() public namespace!: string
    public readonly namespaced: boolean = true
    public state: ModuleState & T
    @Column() public vueComponents!: string[]

    protected constructor(vueComponent: string, namespace: string, state: T) {
        this.state = {...state, namespace, vueComponents: [vueComponent]}
    }

    public get index(): string {
        const split = this.namespace.split('/')
        return split[split.length - 1]
    }

    public defineProperties(): this {
        for (const property of Object.keys(this)) {
            const metadata = Reflect.getMetadata('type', this, property)
            if (typeof metadata === 'string' && metadata === 'column') {
                Object.defineProperty(this, property, {
                    get: () => useNamespacedState(store, this.state.namespace, [property])[property].value,
                    set: value => {
                        useNamespacedMutations(store, this.state.namespace, [property])[property](value)
                    }
                })
            }
        }
        return this
    }

    public register(): this {
        const path: string[] = []
        for (const part of this.state.namespace.split('/')) {
            path.push(part)
            if (!store.hasModule(path))
                store.registerModule(path, path.join('/') === this.state.namespace ? this : {namespaced: true})
        }
        return this
    }
}
