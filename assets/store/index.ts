import type {Actions} from './actions'
import type {Mutations} from './mutation'
import type {State as RootState} from './state'
import type {State as Security} from './security'
import type {State as Notifications} from './notifications'
import type {State} from './state'
import type {Store} from 'vuex'
import {actions} from './actions'
import {createStore} from 'vuex'
import {generateSecurity} from './security'
import {mutations} from './mutation'
import {generateNotifications} from './notifications'
import {state} from './state'

export type {Actions, State, Mutations}
export type {RootState}

export function generateStore(security: Security, notifications:Notifications): Store<State> {
    return createStore<State>({
        actions,
        modules: {notifications: generateNotifications(notifications), security: generateSecurity(security)},
        mutations,
        state,
        strict: process.env.NODE_ENV !== 'production'
    })
}

 export type AppStore = ReturnType<typeof generateStore>
