import type {Response, State} from './state'
import type {Getters} from './getters'
import type {Module} from 'vuex'
import type {Mutations} from './mutation'
import type {State as RootState} from '..'
import {getters} from './getters'

export type {Getters, Mutations, State, Response}

export function generateItem(state: State): Module<State, RootState> {
    return {getters, namespaced: true, state}
}
