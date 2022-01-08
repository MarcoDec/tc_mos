import type {Actions} from './actions'
import type {Mutations} from './mutation'
import type {State as Security} from './security'
import type {State} from './state'
import type {Store} from 'vuex'
import {actions} from './actions'
import {component} from './purchase/component'
import {createStore} from 'vuex'
import {generateSecurity} from './security'
import {mutations} from './mutation'
import {state} from './state'

export type {Actions, State, Mutations}

export function generateStore(security: Security): Store<State> {
    return createStore<State>({
        actions,
        modules: {component, security: generateSecurity(security)},
        mutations,
        state,
        strict: process.env.NODE_ENV !== 'production'
    })
}
