import type {ReadState, State} from './state'
import type {Actions} from './actions'
import type {Getters} from './getters'
import type {Module} from 'vuex'
import type {Mutations} from './mutation'
import type {State as RootState} from '..'
import {actions} from './actions'
import {getters} from './getters'
import {mutations} from './mutation'

export type {Actions, Getters, Mutations, ReadState, State}

export function generateSecurity(state: ReadState): Module<State, RootState> {
    return {actions, getters, mutations, namespaced: true, state}
}
