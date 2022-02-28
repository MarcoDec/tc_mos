import type {DeepReadonly} from '../../types/types'
import type {State as RootState} from '..'
import type {State} from '.'
import type {State as Items} from './supplierItem'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateItem} from './supplierItem'

type ActionContext = DeepReadonly<VuexActionContext<State, RootState>>


export const actions = {
    async fetchItem({commit,dispatch}: ActionContext): Promise<void> {
        const response : Items[]= [
            {
                delete: true,
                composant: 'composant',
                produit: 'produit',
                type: 'radio',
                ref: 'ref',
                quantiteS: 14,
                date: '22/03/2022',
                quantite: 11,
                etat: 'etat',
                texte: 'texte',
                compagnie: 'compagnie',
                update: true,
                id: 1
            },
            {
                delete: true,
                composant: 'composant',
                produit: 'produit',
                type: 'type',
                ref: 'ref',
                quantiteS: 55,
                date: '11/02/2022',
                quantite: 20,
                etat: 'etat',
                texte: 'texte',
                compagnie: 'compagnie',
                update: true,
                id: 2
            },
        ]

       const supplierItems = []
        for (const item of response)
            supplierItems.push(dispatch(
                    'registerModule',
                    {module: generateItem(item), path: ['supplierItems', item.id.toString()]},
                    {root: true}
                ),
            )
        await Promise.all(supplierItems)
    },
}

export type Actions = typeof actions
