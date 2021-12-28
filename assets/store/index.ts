import {ActionTypes, actions} from './actions'
import {MutationTypes, mutations} from './mutation'
import type {Actions} from './actions'
import type {Mutations} from './mutation'
import type {State as Security} from './security'
import type {State} from './state'
import type {Store} from 'vuex'
import {createStore} from 'vuex'
import {generateModule as generateSecurity} from './security'
import {state} from './state'

export type {Actions, State, Mutations}
export {ActionTypes, MutationTypes}

export function generateStore(security: Security): Store<State> {
    return createStore<State>({
        actions,
        modules: {security: generateSecurity(security)},
        mutations,
        state,
        strict: process.env.NODE_ENV !== 'production'
    })
}
