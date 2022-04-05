import {ActionTypes, actions} from './actions'
import type {Getters, GettersValues} from './getters'
import {MutationTypes, mutations} from './mutation'
import type {Actions} from './actions'
import type {Module} from 'vuex'
import type {Mutations} from './mutation'
import type {State as RootState} from '../index'
import type {State} from './state'
import {getters} from './getters'
import {state} from './state'

export const LARGE_SCREEN = 1140

export {ActionTypes, MutationTypes}
export type {Actions, Getters, GettersValues, Mutations, State}

export const gui: Module<State, RootState> = {actions, getters, mutations, namespaced: true, state}
