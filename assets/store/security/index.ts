import type {Module} from '..'
import type {Mutations} from './mutations'
import type {State} from './state'
import {actions} from './actions'
import {getters} from './getters'
import {mutations} from './mutations'

export type {Mutations, State}

export function generateSecurity(state: State): Module<State> {
    return {actions, getters, mutations, namespaced: true, state}
}
