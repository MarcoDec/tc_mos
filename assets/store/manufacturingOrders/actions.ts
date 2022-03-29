import type {State as ManufacturingOrder} from './manufacturingOrder'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateManufacturingOrder} from './manufacturingOrder'

type ActionContext = VuexActionContext<State, RootState>

export const actions = {
    async fetchManufacturingOrder({dispatch}: ActionContext): Promise<void> {
        const response: ManufacturingOrder[] = [
            {
                commande: 'cc',
                compagnie: 'dddd',
                etat: 'refus',
                id: 1,
                indice: 'CAB-1000',
                numero: '3M FRANCE',
                produit: 'CAB-1000',
                quantite: 'cc',
                quantiteF: 'cc',
                show: true,
                traffic: false,
                usine: 'CAB-1000'
            },
            {
                commande: 'cc',
                compagnie: 'dddd',
                etat: 'refus',
                id: 2,
                indice: 'CAB-1000',
                numero: '3M FRANCE',
                produit: 'CAB-1000',
                quantite: 'cc',
                quantiteF: 'cc',
                show: true,
                traffic: false,
                usine: 'CAB-1000'
            },
            {
                commande: 'cc',
                compagnie: 'dddd',
                etat: 'refus',
                id: 3,
                indice: 'CAB-1000',
                numero: '3M FRANCE',
                produit: 'CAB-1000',
                quantite: 'cc',
                quantiteF: 'cc',
                show: true,
                traffic: false,
                usine: 'CAB-1000'
            }
        ]

        const orders = []
        for (const list of response)
            orders.push(
                dispatch(
                    'registerModule',
                    {
                        module: generateManufacturingOrder(list),
                        path: ['manufacturingOrders', list.id.toString()]
                    },
                    {root: true}
                )
            )
        await Promise.all(orders)
    }
}

export type Actions = typeof actions
