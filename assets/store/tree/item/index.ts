import type {Item, State, TreeItemAction} from './state'
import type {Module} from '../..'
import {getters} from './getters'

export type {State}

export function generateItem(
    item: Item,
    moduleName: 'tree/{id}',
    url: string,
    state: TreeItemAction = {opened: false, selected: false}
): Module<State> {
    return {
        getters,
        namespaced: true,
        state: {baseUrl: url, moduleName, url: `${url}/{id}`, violations: [], ...item, ...state}
    }
}
