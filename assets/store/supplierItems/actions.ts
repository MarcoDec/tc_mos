import * as Cookies from '../../cookie'
import type {DeepReadonly} from '../../types/types'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {fetchApi} from '../../api'
import {generateItem} from './supplierItem'
import type {TableItem} from '../../types/app-collection-table'

type ActionContext = DeepReadonly<VuexActionContext<State, RootState>>


export const actions = {
    async fetchItem({commit,dispatch}: ActionContext): Promise<void> {
        const response : State= [
            {
                delete: true,
                composant: 'composant',
                produit: 'produit',
                type: 'type',
                ref: 'ref',
                quantiteS: 14,
                date: 'date',
                quantite: 11,
                etat: 'etat',
                texte: 'texte',
                compagnie: 'compagnie',
                update: false,
                id: 1
            },
            {
                delete: true,
                composant: 'composant',
                produit: 'produit',
                type: 'type',
                ref: 'ref',
                quantiteS: 55,
                date: 'date',
                quantite: 20,
                etat: 'etat',
                texte: 'texte',
                compagnie: 'compagnie',
                update: false,
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
        commit('listItem',response)
    },
}

export type Actions = typeof actions
