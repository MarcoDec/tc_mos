import type {Actions} from './actions'
import type {Mutations} from './mutation'
import type {State as Security} from './security'
import type {State} from './state'
import type {Store} from 'vuex'
import {actions} from './actions'
import {blCustomerOrderItems} from './blCustomerOrderItems'
import {createStore} from 'vuex'
import {customerOrderItems} from './customerOrderItems'
import {facturesCustomerOrderItems} from './facturesCustomerOrderItems'
import {generateSecurity} from './security'
import {mutations} from './mutation'
import {ofCustomerOrderItems} from './ofCustomerOrderItems'
import {state} from './state'

export type {Actions, State, Mutations}
export function generateStore(security: Security): Store<State> {
    return createStore<State>({
        actions,
        modules: {blCustomerOrderItems, customerOrderItems, facturesCustomerOrderItems, ofCustomerOrderItems, security: generateSecurity(security)},
        mutations,
        state,
        strict: process.env.NODE_ENV !== 'production'
    })
}
export type AppStore = ReturnType<typeof generateStore>
