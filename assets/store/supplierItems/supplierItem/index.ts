import type {Getters} from './getters'
import type {Module} from 'vuex'
import type {State} from './state'
import type {State as RootState} from '..'
import {getters} from './getters'

export type {Getters, State}

export function generateItem(state: State): Module<State, RootState> {
    return {getters, namespaced: true, state}
}
