import type {State as Customer} from './customer'
import {generateModule} from './customer'
import store from '../../index'

export enum ActionTypes {
    FETCH_CUSTOMER = 'FETCH_CUSTOMER'
}


export const actions = {
    async [ActionTypes.FETCH_CUSTOMER](): Promise<void> {

        const response: Customer[] = [
            { name: 'customer1', '@id': '1', id: 1},
            { name: 'customer2', '@id':'2', id: 2},
            { name: 'customer3', '@id': '3', id: 3}
        ]
        for (const customer of response)
            store.registerModule(['customers', customer.id.toString()], generateModule(customer))
    }

}

export type Actions = typeof actions