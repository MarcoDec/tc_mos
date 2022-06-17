import type {DeepReadonly} from '../../types/types'
import type {State as Items} from './warehouseListItem'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateItem} from './warehouseListItem'

type ActionContext = DeepReadonly<VuexActionContext<State, RootState>>

export const actions = {
    async fetchItem({dispatch}: ActionContext): Promise<void> {
        const response: Items[] = [
            {
                delete: false,
                famille: 'camion',
                id: 1,
                name: 'CAMION FR-MD',
                update: false,
                update2: true
            },
            {
                delete: false,
                famille: 'prison',
                id: 2,
                name: 'Prison',
                update: false,
                update2: true
            }
        ]

        const warehouseListItems = []
        for (const item of response)
            warehouseListItems.push(dispatch(
                'registerModule',
                {module: generateItem(item), path: ['warehouseListItems', item.id.toString()]},
                {root: true}
            ))
        await Promise.all(warehouseListItems)
    }
}

export type Actions = typeof actions
