import {ActionTypes, actions} from './actions'
import type {Actions} from './actions'
import type {Getters} from './getters'
import type {Module} from 'vuex'
import type {Mutations} from './mutation'
import type {State as RootState} from '..'
import type {State} from './state'
import {getters} from './getters'
import {mutations,MutationTypes} from './mutation'

export type {Actions, Getters, Mutations, State}
export {ActionTypes,MutationTypes}

export const notifications: Module<State, RootState> = {actions, getters, mutations, namespaced: true}
