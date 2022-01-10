import type {Actions} from './actions'
import type {Module} from 'vuex'
import type {State as RootState} from '../../..'
import type {State} from './state'
import {actions} from './actions'

export type {Actions, State}

export const families: Module<State, RootState> = {actions, namespaced: true}
