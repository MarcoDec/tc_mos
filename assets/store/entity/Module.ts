import 'reflect-metadata'
import {useNamespacedMutations, useNamespacedState} from 'vuex-composition-helpers'
import store from '../store'

export default abstract class Module<T = unknown> {
    public readonly namespaced: boolean = true

    protected constructor(public readonly namespace: string, public state: T) {
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
                    get: () => useNamespacedState(store, this.namespace, [property])[property].value,
                    set: value => {
                        useNamespacedMutations(store, this.namespace, [property])[property](value)
                    }
                })
            }
        }
        return this
    }

    public register(): this {
        const path: string[] = []
        for (const part of this.namespace.split('/')) {
            path.push(part)
            if (!store.hasModule(path))
                store.registerModule(path, path.join('/') === this.namespace ? this : {namespaced: true})
        }
        return this
    }
}

export function Column(): ReturnType<typeof Reflect.metadata> {
    return Reflect.metadata('type', 'column')
}
