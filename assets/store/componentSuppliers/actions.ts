import type {ComputedGetters, State} from '.'
import type {StoreActionContext} from '..'
import type {State as Items} from './componentSupplier'
import {generateItem} from './componentSupplier'

declare type ActionContext = StoreActionContext<State, ComputedGetters>

export const actions = {
    async fetchItem({dispatch}: ActionContext): Promise<void> {
        const response: Items[] = [
            {
                delete: true,
                proportion: 'aaaaaa',
                delai: 5,
                moq: 1,
                poidsCu: 'bbbbb',
                reference: 'ccc',
                indice: 1,
                prices:['componentSupplierPrices/1','componentSupplierPrices/3'],
                name: 'CAMION FR-MD',
                update: true,
                update2: false,
                id:1
            },
            {
                delete: true,
                proportion: 'vvvvvv',
                delai: 15,
                moq: 2,
                poidsCu: 'aaaa',
                reference: 'wwwww',
                indice: 2,
                prices:['componentSupplierPrices/2'],
                name: 'CAMION',
                update: true,
                update2: false,
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
