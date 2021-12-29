import {ActionTypes, actions} from './actions'
import {MutationTypes, mutations} from './mutation'
import type {Actions} from './actions'
import type {Getters} from './getters'
import type {Module} from 'vuex'
import type {Mutations} from './mutation'
import type {State as RootState} from '..'
import type {State} from './state'
import {getters} from './getters'

export type {Actions, Getters, Mutations, State}
export {ActionTypes, MutationTypes}

export function generateSecurity(state: State): Module<State, RootState> {
    return {actions, getters, mutations, namespaced: true, state}
}
