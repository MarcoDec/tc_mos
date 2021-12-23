import type {State as RootState} from './state'
import {component} from './purchase/component'
import {createStore} from 'vuex'
import {mutations} from './mutation'
import {state} from './state'
import {users} from './security'

export type {RootState}

const store = createStore<RootState>({
    modules: {component, users},
    mutations,
    state,
    strict: process.env.NODE_ENV !== 'production'
})

export default store
