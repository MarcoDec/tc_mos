import {ActionTypes, actions} from './actions'
import {MutationTypes, mutations} from './mutation'
import type {Actions} from './actions'
import type {Mutations} from './mutation'
import type {State as RootState} from './state'
import {createStore} from 'vuex'
import {state} from './state'
import {users} from './security'

export type {Actions, RootState, Mutations}
export {ActionTypes, MutationTypes}

const store = createStore<RootState>({
    actions,
    modules: {users},
    mutations,
    state,
    strict: process.env.NODE_ENV !== 'production'
})

export default store
