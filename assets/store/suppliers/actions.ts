import type {DeepReadonly} from '../../types/types'
import type {State as Suppliers} from './supplier'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateSupplier} from './supplier'

type ActionContext = DeepReadonly<VuexActionContext<State, RootState>>

export const actions = {
    async fetchSuppliers({dispatch}: ActionContext): Promise<void> {
        const response: Suppliers[] = [
            { 
                etat: 'etat',
                delete: true,
                id: 1,
                nom: '3M FRANCE',
            },
            {
                etat: 'etat',
                delete: true,
                id: 2,
                nom: 'ABB',
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