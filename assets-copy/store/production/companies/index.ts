import {ActionTypes, actions} from './actions'
import type {Actions} from './actions'
import type {Getters} from './getters'
import type {Module} from 'vuex'
import type {RootState} from '../../index'
import type {State} from './state'
import {getters} from './getters'

export {ActionTypes, Actions, Getters, State}

export const company: Module<State, RootState> = {actions, getters, namespaced: true}