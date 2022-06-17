import type {ComputedGetters, Getters} from './getters'
import type {Actions} from './actions'
import type {Module} from 'vuex'
import type {State as RootState} from '../..'
import type {State} from './state'
import {actions} from './actions'
import {getters} from './getters'

export type {Actions, Getters, ComputedGetters, State}

export const compagnies: Module<State, RootState> = {actions, getters, namespaced: true}
