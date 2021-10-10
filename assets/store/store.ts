import type {Module} from 'vuex'
import {createStore} from 'vuex'

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export const store = createStore<any>({strict: process.env.NODE_ENV !== 'production'})

// eslint-disable-next-line @typescript-eslint/no-explicit-any
function register(path: string[] | string, module: Module<any, any>): void {
    module.namespaced = true
    if (Array.isArray(path)) {
        if (!store.hasModule(path))
            store.registerModule(path, module)
    } else if (!store.hasModule(path))
        store.registerModule(path, module)
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export function registerModule(path: string[], module: Module<any, any>): void {
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
