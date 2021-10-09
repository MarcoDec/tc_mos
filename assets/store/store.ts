import type {Module} from 'vuex'
import {createStore} from 'vuex'

// eslint-disable-next-line @typescript-eslint/no-explicit-any
type Unkown = Record<string, any>

export const store = createStore<Unkown>({strict: process.env.NODE_ENV !== 'production'})

function register(path: string[] | string, module: Module<Unkown, Unkown>): void {
    module.namespaced = true
    if (Array.isArray(path)) {
        if (!store.hasModule(path))
            store.registerModule(path, module)
    } else if (!store.hasModule(path))
        store.registerModule(path, module)
}

export function registerModule(path: string[], module: Module<Unkown, Unkown>): void {
    const last = path.length - 1
    for (let i = 0; i < path.length; i++) {
        if (i === 0)
            register(path[i], {})
        else if (i === last)
            register(path, module)
        else
            register(path.slice(0, i), {})
    }
}
