import type {State as RootState} from './state'
import {createStore} from 'vuex'
import {mutations} from './mutation'
import {state} from './state'
import {users} from './security'

export type {RootState}

const store = createStore<RootState>({modules: {users}, mutations, state, strict: process.env.NODE_ENV !== 'production'})
export default store
