import type {DeepReadonly} from '../../types/types'
import type {State as Items} from './componentSupplierPrice'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateItem} from './componentSupplierPrice'

type ActionContext = DeepReadonly<VuexActionContext<State, RootState>>

export const actions = {
    async fetchItem({dispatch}: ActionContext): Promise<void> {
        const response: Items[] = [
            {
                delete: false,
                price: 1000,
                quantite:  100,
                ref: 'afsfsfss',
                id: 1,
                update: true,
                update2: false
            },
            {
                delete: false,
                price: 100,
                quantite:  50,
                ref: 'azerty',
                id: 2,
                update: true,
                update2: false
            }
        ]

        const componentSupplierPrices = []
        for (const item of response)
            componentSupplierPrices.push(dispatch(
                'registerModule',
                {module: generateItem(item), path: ['componentSupplierPrices', item.id.toString()]},
                {root: true}
            ))
        await Promise.all(componentSupplierPrices)
    }
}

export type Actions = typeof actions
