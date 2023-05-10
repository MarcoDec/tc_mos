import {useGetters, useMutations} from 'vuex-composition-helpers'
import type {Module} from 'vuex'
import type {Ref} from 'vue'
import User from './security/User'
import type {UserResponse} from './security/User'
import {createStore} from 'vuex'

type GlobalState = {
    user: User | null
}

export const store = createStore<GlobalState>({
    getters: {
        hasUser(state: GlobalState): boolean {
            return state.user !== null
        }
    },
    mutations: {
        connect(state: GlobalState, user: User): void {
            state.user = user
        }
    },
    state: {user: null},
    strict: process.env.NODE_ENV !== 'production'
})

export function connect(user: UserResponse): void {
    useMutations(store, ['connect']).connect(new User(user))
}

export function hasUser(): Ref<boolean> {
    return useGetters(store, ['hasUser']).hasUser
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
function register(path: string[] | string, module: Module<any, GlobalState>): void {
    module.namespaced = true
    if (Array.isArray(path)) {
        if (!store.hasModule(path))
            store.registerModule(path, module)
    } else if (!store.hasModule(path))
        store.registerModule(path, module)
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export function registerModule(path: string[], module: Module<any, GlobalState>): void {
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
