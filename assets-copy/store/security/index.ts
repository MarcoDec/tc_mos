import {ActionTypes, actions} from './actions'
import {MutationTypes, mutations} from './mutation'
import type {Actions} from './actions'
import type {Getters} from './getters'
import type {Module} from 'vuex'
import type {Mutations} from './mutation'
import type {RootState} from '../index'
import type {State} from './state'
import {getters} from './getters'
import {state} from './state'

export type {Actions, Getters, Mutations, State}
export {ActionTypes, MutationTypes}

export const users: Module<State, RootState> = {actions, getters, mutations, namespaced: true, state}