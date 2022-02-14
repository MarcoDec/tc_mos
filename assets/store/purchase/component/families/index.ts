import type {ComputedGetters, Getters} from './getters'
import type {Actions} from './actions'
import type {Module} from '../../../index'
import type {State} from './state'
import {actions} from './actions'
import {getters} from './getters'

export type {Actions, ComputedGetters, Getters, State}

export function generateFamilies(moduleName: [string, ...string[]]): Module<State> {
    return {actions, getters, namespaced: true, state: {moduleName: moduleName.join('/')}}
}
