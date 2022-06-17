import type {Actions} from './actions'
import type {Getters} from './getters'
import type {Module} from 'vuex'
import type {Mutations} from './mutation'
import type {State as RootState} from '..'
import type {State} from './state'
import {actions} from './actions'
import {getters} from './getters'
import {mutations} from './mutation'

export type {Actions, Getters, Mutations, State}

export const warehouseListItems: Module<State, RootState> = {actions, getters, mutations, namespaced: true}
