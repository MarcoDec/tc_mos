import type {State, Statewarehouse} from './state'
import type {Getters} from './getters'
import type {Module} from 'vuex'
import type {Mutations} from './mutation'
import type {State as RootState} from '..'
import {getters} from './getters'

export type {Getters, Mutations, State, Statewarehouse}

export function generateItem(state: State): Module<State, RootState> {
    return {getters, namespaced: true, state}
}
