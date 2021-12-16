import type {State as UserState} from './security'
import {createStore} from 'vuex'
import {users} from './security'

export type RootState = {
    user: UserState
}

const store = createStore<RootState>({modules: {users}, strict: process.env.NODE_ENV !== 'production'})

export default store
