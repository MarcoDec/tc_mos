import type {Getters} from './getters'
import type {Module} from 'vuex'
import type {State as RootState} from '..'
import type {State} from './state'
import {getters} from './getters'

export type {Getters, State}

export function generateAttribut(state: State): Module<State, RootState> {
    return {getters, namespaced: true, state}
}
