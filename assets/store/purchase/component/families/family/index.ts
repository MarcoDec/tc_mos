import type {ComputedGetters, Getters} from './getters'
import type {Family, State, TreeItemAction} from './state'
import type {Actions} from './actions'
import type {Module} from '../../../../index'
import type {Mutations} from './mutations'
import {actions} from './actions'
import {getters} from './getters'
import {mutations} from './mutations'

export type {Actions, ComputedGetters, Getters, Mutations, State}

export function generateFamily(
    moduleName: string,
    parentModuleName: string,
    family: Family,
    state: TreeItemAction = {opened: false, selected: false}
): Module<State> {
    return {
        actions,
        getters,
        mutations,
        namespaced: true,
        state: {moduleName, parentModuleName, violations: [], ...family, ...state}
    }
}
