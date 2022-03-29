import type {State as Produit} from './produit'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateProduit} from './produit'

type ActionContext = VuexActionContext<State, RootState>

export const actions = {
    async fetchProduits({dispatch}: ActionContext): Promise<void> {
        const response: Produit[] = [
            {
                besoins: '',
                client: 'RENAULT',
                date: 'string',
                etat: '',
                famille: 'Cables',
                id: 1,
                img: 'string',
                indice: 'rrrr',
                ref: '1176481X',
                show: true,
                stock: 'ddd',
                traffic: true,
                type: 'rrr'
            },
            {
                besoins: '',
                client: 'DACIA',
                date: 'string',
                etat: 'valide',
                famille: 'Cables',
                id: 2,
                img: 'string',
                indice: 'rrrr',
                ref: '122508040X',
                show: true,
                stock: 'ddd',
                traffic: true,
                type: 'rrr'
            },
            {
                besoins: '',
                client: 'DACIA',
                date: 'string',
                etat: '',
                famille: 'Cables',
                id: 3,
                img: 'string',
                indice: 'rrrr',
                ref: '122508040X',
                show: true,
                stock: 'ddd',
                traffic: true,
                type: 'rrr'
            }

        ]

        const produits = []
        for (const list of response)
            produits.push(dispatch(
                'registerModule',
                {module: generateProduit(list), path: ['produits', list.id.toString()]},
                {root: true}
            ))
        await Promise.all(produits)
    }

}

export type Actions = typeof actions
