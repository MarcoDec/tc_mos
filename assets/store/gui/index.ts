import type {Module} from 'vuex'
import type {RootState} from '../index'
import type {State} from './state'
import {getters} from './getters'
import {mutations} from './mutations'
import {state} from './state'

export {State}

export const module: Module<State, RootState> = {getters, mutations, namespaced: true, state}
