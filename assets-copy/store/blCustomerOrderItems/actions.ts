import type {DeepReadonly} from '../../types/types'
import type {State as Items} from './blCustomerOrderItem'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateItem} from './blCustomerOrderItem'

type ActionContext = DeepReadonly<VuexActionContext<State, RootState>>

export const actions = {
    async fetchItem({dispatch}: ActionContext): Promise<void> {
        const response: Items[] = [
            {
                currentPlace: 'in_creation',
                delete: true,
                departureDate: '2017-06-20',
                id: 1,
                number: 10011,
                update: false
            },
            {
                currentPlace: 'in_creation',
                delete: true,
                departureDate: '2017-07-04',
                id: 2,
                number: 10080,
                update: false
            }
        ]

        const blCustomerOrderItems = []
        for (const item of response)
            blCustomerOrderItems.push(dispatch(
                'registerModule',
                {module: generateItem(item), path: ['blCustomerOrderItems', item.id.toString()]},
                {root: true}
            ))
        await Promise.all(blCustomerOrderItems)
    }
}

export type Actions = typeof actions
