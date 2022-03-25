import type {ComputedGetters, Getters} from './getters'
import type {Actions} from './actions'
import type {Module} from 'vuex'
import type {Mutations} from './mutation'
import type {State as RootState} from '..'
import type {State} from './state'
import {actions} from './actions'
import {getters} from './getters'
import {mutations} from './mutation'

export type {Actions, ComputedGetters, Getters, Mutations, State}

export function generateSecurity(state: State): Module<State, RootState> {
    return {actions, getters, mutations, namespaced: true, state}
}
