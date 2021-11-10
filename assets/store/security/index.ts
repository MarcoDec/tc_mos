import type {Module} from 'vuex'
import type {RootState} from '../index'
import type {State} from './state'
import {actions} from './actions'
import {getters} from './getters'
import {mutations} from './mutations'
import {state} from './state'

export {State}

export const module: Module<State, RootState> = {actions, getters, mutations, namespaced: true, state}
