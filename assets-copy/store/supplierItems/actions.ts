import type {DeepReadonly} from '../../types/types'
import type {State as Items} from './supplierItem'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateItem} from './supplierItem'

type ActionContext = DeepReadonly<VuexActionContext<State, RootState>>

export const actions = {
    async fetchItem({dispatch}: ActionContext): Promise<void> {
        const response: Items[] = [
            {
                compagnie: 'compagnie',
                composant: 'composant',
                date: '2022-03-11',
                delete: true,
                etat: 'etat',
                id: 1,
                produit: 'produit',
                quantite: 11,
                quantiteS: 14,
                ref: 'ref',
                texte: 'texte',
                type: 'radio',
                update: true

            },
            {
                compagnie: 'compagnie',
                composant: 'composant',
                date: '2022-03-19',
                delete: true,
                etat: 'etat',
                id: 2,
                produit: 'produit',
                quantite: 20,
                quantiteS: 14,
                ref: 'ref',
                texte: 'texte',
                type: 'radio',
                update: true
            }
        ]

        const supplierItems = []
        for (const item of response)
            supplierItems.push(dispatch(
                'registerModule',
                {module: generateItem(item), path: ['supplierItems', item.id.toString()]},
                {root: true}
            ))
        await Promise.all(supplierItems)
    }
}

export type Actions = typeof actions
