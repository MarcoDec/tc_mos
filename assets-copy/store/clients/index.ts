import type {Actions} from './actions'
import type {Getters} from './getters'
import type {Module} from 'vuex'
import type {State as RootState} from '..'
import type {State} from './state'
import {actions} from './actions'
import {getters} from './getters'

export type {Actions, Getters, State}

export const clients: Module<State, RootState> = {actions, getters, namespaced: true}
