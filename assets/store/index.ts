import type {Actions, StoreActionContext} from './actions'
import type {ReadState, State} from './state'
import type {DeepReadonly} from '../types/types'
import type {Mutations} from './mutation'
import type {State as Security} from './security'
import type {Store} from 'vuex'
import {actions} from './actions'
import {createStore} from 'vuex'
import {families} from './purchase/component/families'
import {generateSecurity} from './security'
import {mutations} from './mutation'
import {state} from './state'

export type {Actions, ReadState, Mutations, State, StoreActionContext}

export function generateStore(security: Readonly<Security>): Store<State> {
    return createStore<State>({
        actions,
        modules: {families, security: generateSecurity(security)},
        mutations,
        state,
        strict: process.env.NODE_ENV !== 'production'
    })
}

export type AppStore = ReturnType<typeof generateStore>
export type Getters = AppStore['getters']
export type ReadGetters = DeepReadonly<Getters>
