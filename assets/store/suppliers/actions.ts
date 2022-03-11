import type {State as Suppliers} from './supplier'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateSupplier} from './supplier'

type ActionContext = VuexActionContext<State, RootState>

export const actions = {
    async fetchSuppliers({dispatch}: ActionContext): Promise<void> {
        const response: Suppliers[] = [
            { 
                etat: 'refus',
                id: 1,
                nom: '3M FRANCE',
                show: true,
                traffic: false
            },
            {
                etat: 'attente',
                id: 2,
                nom: 'ABB',
                show: true,
                traffic: true
            },
            {
                etat: 'valide',
                id: 3,
                nom: 'ABB',
                show: true,
                traffic: true
            }
        ]

        const suppliers = []
        for (const list of response)
            suppliers.push(dispatch(
                'registerModule',
                {module: generateSupplier(list), path: ['suppliers', list.id.toString()]},
                {root: true}
            ))
        await Promise.all(suppliers)
    }
}

export type Actions = typeof actions