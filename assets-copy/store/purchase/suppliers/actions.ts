import type {State as Supplier} from './supplier'
import {generateModule} from './supplier'
import store from '../../index'

export enum ActionTypes {
    FETCH_SUPPLIEER = 'FETCH_SUPPLIEER'
}


export const actions = {
    async [ActionTypes.FETCH_SUPPLIEER](): Promise<void> {

        const response: Supplier[] = [
            { name: 'supplier1', '@id': '1', id: 1},
            { name: 'supplier2', '@id':'2', id: 2},
            { name: 'supplier3', '@id': '3', id: 3}
        ]
        for (const supplier of response)
            store.registerModule(['suppliers', supplier.id.toString()], generateModule(supplier))
    }

}

export type Actions = typeof actions