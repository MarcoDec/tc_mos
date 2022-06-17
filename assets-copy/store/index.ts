import type {Actions} from './actions'
import type {Mutations} from './mutation'
import type {State as Overview} from './overview'
import type {State as Security} from './security'
import type {State} from './state'
import type {Store} from 'vuex'
import {actions} from './actions'
import {createStore} from 'vuex'
import {generateOverview} from './overview'
import {generateSecurity} from './security'
import {mutations} from './mutation'
import {state} from './state'

export type {Actions, State, Mutations}

export function generateStore(security: Security, overview: Overview): Store<State> {
    console.debug('overview', overview)
    return createStore<State>({
        actions,
        modules: {overview: generateOverview(overview), security: generateSecurity(security)},
        mutations,
        state,
        strict: process.env.NODE_ENV !== 'production'
    })
}
