import type {Actions} from './actions'
import type {Mutations} from './mutation'
import type {State as RootState} from './state'
import type {State as Security} from './security'
import type {State} from './state'
import type {Store} from 'vuex'
import {actions} from './actions'
import {createStore} from 'vuex'
import {generateSecurity} from './security'
import {supplierItems} from './supplierItems'
import {mutations} from './mutation'
import {state} from './state'

export type {Actions, State, Mutations}
export type {RootState}

export function generateStore(security: Security): Store<State> {
    return createStore<State>({
        actions,
        modules: {supplierItems,security: generateSecurity(security)},
        mutations,
        state,
        strict: process.env.NODE_ENV !== 'production'
    })
}
export type AppStore = ReturnType<typeof generateStore>
