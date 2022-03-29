import type {ComputedGetters, Getters} from './getters'
import type {Item, State, TreeItemAction} from './state'
import type {Actions} from './actions'
import type {Module} from '../..'
import type {Mutations} from './mutations'
import {actions} from './actions'
import {getters} from './getters'
import {mutations} from './mutations'

export type {Actions, ComputedGetters, Getters, Item, Mutations, State}

export function generateItem(
    moduleName: string,
    parentModuleName: string,
    item: Item,
    url: string,
    state: TreeItemAction = {opened: false, selected: false}
): Module<State> {
    return {
        actions,
        getters,
        mutations,
        namespaced: true,
        state: {baseUrl: url, moduleName, parentModuleName, url: `${url}/{id}`, violations: [], ...item, ...state}
    }
}
