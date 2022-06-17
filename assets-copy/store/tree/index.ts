import type {ComputedGetters, Getters} from './getters'
import type {Actions} from './actions'
import type {Module} from '..'
import type {Mutations} from './mutations'
import type {State} from './state'
import {actions} from './actions'
import {getters} from './getters'
import {mutations} from './mutations'

export type {Actions, ComputedGetters, Getters, Mutations, State}

export function generateTree(moduleName: [string, ...string[]], url: string): Module<State> {
    return {
        actions,
        getters,
        mutations,
        namespaced: true,
        state: {moduleName: moduleName.join('/'), url, violations: []}
    }
}
