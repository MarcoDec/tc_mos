import {MutationTypes, mutations} from './mutation'
import type {Getters} from './getters'
import type {Module} from 'vuex'
import type {Mutations} from './mutation'
import type {RootState} from '../../index'
import type {State} from './state'
import {getters} from './getters'

export {Getters, MutationTypes, Mutations, State}
export function generateModule(state: State): Module<State, RootState>{
    return {getters, mutations, namespaced: true, state}
}
