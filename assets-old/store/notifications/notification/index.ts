import {MutationTypes, mutations} from './mutation'
import type {Getters} from './getters'
import type {Module} from 'vuex'
import type {Mutations} from './mutation'
import type {State as RootState} from '../..'
import type {State} from './state'
import {getters} from './getters'

export type {Getters, Mutations, State}
export {MutationTypes}

export function generateModule(state: State): Module<State, RootState> {
    return {getters, mutations, namespaced: true, state}
}
