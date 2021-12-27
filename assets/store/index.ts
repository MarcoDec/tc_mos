import {MutationTypes, mutations} from './mutation'
import type {Mutations} from './mutation'
import type {State as RootState} from './state'
import {createStore} from 'vuex'
import {state} from './state'
import {users} from './security'

export type {RootState, Mutations}
export {MutationTypes}

const store = createStore<RootState>({
    modules: {users},
    mutations,
    state,
    strict: process.env.NODE_ENV !== 'production'
})

export default store
