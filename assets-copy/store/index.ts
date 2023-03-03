import type {State as RootState} from './state'
import {createStore} from 'vuex'
import {mutations} from './mutation'
import {state} from './state'
import {users} from './security'
import {events} from './events'

export type {RootState}
const store = createStore<RootState>({modules: {users,events}, mutations, state, strict: process.env.NODE_ENV !== 'production'})
export type RootGetters = typeof store.getters

export default store
