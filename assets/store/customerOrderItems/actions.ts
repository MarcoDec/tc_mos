import type {DeepReadonly} from '../../types/types'
import type {State as Items} from './customerOrderItem'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateItem} from './customerOrderItem'

type ActionContext = DeepReadonly<VuexActionContext<State, RootState>>

export const actions = {
    async fetchItem({dispatch}: ActionContext): Promise<void> {
        const response: Items[] = [
            {
                dateLivraisonConfirmée: '04/07/2019',
                dateLivraisonSouhaitée: '25/07/2019',
                delete: true,
                description: 'description',
                etat: 'etat',
                id: 1,
                produit: 'produit',
                quantitéConfirmée: 14,
                quantitéSouhaitée: 666,
                ref: 'ref',
                update: false
            },
            {
                dateLivraisonConfirmée: '04/04/2019',
                dateLivraisonSouhaitée: '25/04/2019',
                delete: true,
                description: 'descriptionnn',
                etat: 'etatttt',
                id: 2,
                produit: 'produittttt',
                quantitéConfirmée: 14,
                quantitéSouhaitée: 666,
                ref: 'refffff',
                update: false
            }
        ]

        const customerOrderItems = []
        for (const item of response)
            customerOrderItems.push(dispatch(
                'registerModule',
                {module: generateItem(item), path: ['customerOrderItems', item.id.toString()]},
                {root: true}
            ))
        await Promise.all(customerOrderItems)
    }
}

export type Actions = typeof actions
