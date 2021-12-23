import {ActionTypes, actions} from './actions'
import {MutationTypes, mutations} from './mutations'
import type {Actions} from './actions'
import type {Getters} from './getters'
import type {Module} from 'vuex'
import type {Mutations} from './mutations'
import type {RootState} from '../../index'
import type {State} from './state'
import {getters} from './getters'
import {state} from './state'

export type {Actions, Getters, Mutations, State}
export {ActionTypes, MutationTypes}

export const component: Module<State, RootState> = {actions, getters, mutations, namespaced: true, state}
