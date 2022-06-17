import type {ReadState, State} from './state'
import type {Actions} from './actions'
import type {Getters} from './getters'
import type {Module} from 'vuex'
import type {State as RootState} from '../..'
import {actions} from './actions'
import {getters} from './getters'

export type {Actions, Getters, ReadState, State}

export const settings: Module<State, RootState> = {actions, getters, namespaced: true}
