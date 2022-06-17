import type {State as Customers} from './customer'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateCustomer} from './customer'

type ActionContext = VuexActionContext<State, RootState>

export const actions = {
    async fetchCustomers({dispatch}: ActionContext): Promise<void> {
        const response: Customers[] = [
            {
                etat: 'refus',
                id: 1,
                nom: 'AML',
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
                nom: 'HUAWEI',
                show: true,
                traffic: true
            }
        ]

        const customers = []
        for (const list of response)
            customers.push(dispatch(
                'registerModule',
                {module: generateCustomer(list), path: ['customers', list.id.toString()]},
                {root: true}
            ))
        await Promise.all(customers)
    }
}

export type Actions = typeof actions
