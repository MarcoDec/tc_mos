import {ActionTypes, actions} from './actions'
import type {Actions} from './actions'
import type {Getters} from './getters'
import type {Module} from 'vuex'
import type {State as RootState} from '..'
import type {State} from './state'
import {getters} from './getters'

export type {Actions, Getters, State}
export {ActionTypes}

export const notifications: Module<State, RootState> = {actions, getters, namespaced: true}
