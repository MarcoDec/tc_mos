import type {Store} from 'vuex'
import {actions} from './actions'
import {createStore} from 'vuex'
import {mutations} from './mutations'
import {state} from './state'

export function generateStore(): Store {
    return createStore({actions, mutations, state, strict: process.env.NODE_ENV !== 'production'})
}
