import type {State as NotifState} from './notifications'
import type {State as UserState} from './security'
import {createStore} from 'vuex'
import {module as notifications} from './notifications'

import {module as users} from './security'

export type RootState = {
    user: UserState;
    notifications: NotifState;
}

const store = createStore<RootState>({modules: {notifications, users}, strict: process.env.NODE_ENV !== 'production'})

export default store
