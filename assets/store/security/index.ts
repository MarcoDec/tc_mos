import {ActionTypes, actions} from './actions'
import {MutationTypes, mutations} from './mutations'
import type {Actions} from './actions'
import type {Getters} from './getters'
import type {Module} from 'vuex'
import type {Mutations} from './mutations'
import type {RootState} from '../index'
import type {State} from './state'
import {getters} from './getters'
import {state} from './state'

export {ActionTypes, Actions, Getters, MutationTypes, Mutations, State}

export const module: Module<State, RootState> = {actions, getters, mutations, namespaced: true, state}
