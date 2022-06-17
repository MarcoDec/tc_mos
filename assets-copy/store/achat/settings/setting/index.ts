import type {ReadState, State} from './state'
import type {Getters} from './getters'
import type {Module} from 'vuex'
import type {State as RootState} from '../../..'
import {getters} from './getters'

export type {Getters, ReadState, State}

export function generateSetting(state: ReadState): Module<State, RootState> {
    return {getters, namespaced: true, state}
}
