import type {DeepReadonly} from '../../types/types'
import type {State as Items} from './warehouseStocksItem'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateItem} from './warehouseStocksItem'

type ActionContext = DeepReadonly<VuexActionContext<State, RootState>>

export const actions = {
    async fetchItem({dispatch}: ActionContext): Promise<void> {
        const response: Items[] = [
            {
                composant: {id: 100, ref: 'CAB-1000'},
                deletee: true,
                id: 1,
                localisation: 'P01',
                numeroDeSerie: '20181026',
                prison: true,
                produit: null,
                quantite: 15,
                update: true,
                update2: false
            },
            {
                composant: {id: 100, ref: 'CAB-1000'},
                deletee: true,
                id: 2,
                localisation: 'P01',
                numeroDeSerie: '20181026',
                prison: true,
                produit: null,
                quantite: 10,
                update: true,
                update2: false
            },
            {
                composant: null,
                deletee: true,
                id: 3,
                localisation: 'P01',
                numeroDeSerie: '20181023',
                prison: true,
                produit: {id: 10, ref: '1188481x'},
                quantite: 7,
                update: true,
                update2: false
            },
            {
                composant: null,
                deletee: true,
                id: 4,
                localisation: 'P01',
                numeroDeSerie: '20181023',
                prison: true,
                produit: {id: 10, ref: '1188481x'},
                quantite: 10,
                update: true,
                update2: false
            },
            {
                composant: null,
                deletee: true,
                id: 5,
                localisation: 'P01',
                numeroDeSerie: '20181023',
                prison: true,
                produit: {id: 20, ref: 'ywx'},
                quantite: 10,
                update: true,
                update2: false
            }
        ]

        const warehouseStocksItems = []
        for (const item of response)
            warehouseStocksItems.push(dispatch(
                'registerModule',
                {module: generateItem(item), path: ['warehouseStocksItems', item.id.toString()]},
                {root: true}
            ))
        await Promise.all(warehouseStocksItems)
    }
}

export type Actions = typeof actions
