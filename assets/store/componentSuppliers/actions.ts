import type {DeepReadonly} from '../../types/types'
import type {State as Items} from './componentSupplier'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateItem} from './componentSupplier'

type ActionContext = DeepReadonly<VuexActionContext<State, RootState>>

export const actions = {
    async fetchItem({dispatch}: ActionContext): Promise<void> {
        const response: Items[] = [
            {
                delete: false,
                proportion: 'aaaaaa',
                delai: 5,
                moq: 1,
                poidsCu: 'bbbbb',
                reference: 'ccc',
                indice: 1,
                prices:['component-supplier-prices/1'],
                name: 'CAMION FR-MD',
                update: false,
                update2: true,
                id:1
            },
            {
                delete: false,
                proportion: 'vvvvvv',
                delai: 15,
                moq: 2,
                poidsCu: 'aaaa',
                reference: 'wwwww',
                indice: 2,
                prices:['component-supplier-prices/2'],
                name: 'CAMION',
                update: false,
                update2: true,
                id:2
            }
        ]

        const componentSuppliers = []
        for (const item of response)
            componentSuppliers.push(dispatch(
                'registerModule',
                {module: generateItem(item), path: ['componentSuppliers', item.id.toString()]},
                {root: true}
            ))
        await Promise.all(componentSuppliers)
    }
}

export type Actions = typeof actions
