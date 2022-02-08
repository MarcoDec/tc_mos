import type {DeepReadonly} from '../../types/types'
import type {State as Items} from './ofCustomerOrderItem'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateItem} from './ofCustomerOrderItem'

type ActionContext = DeepReadonly<VuexActionContext<State, RootState>>

export const actions = {
    async fetchItem({dispatch}: ActionContext): Promise<void> {
        const response: Items[] = [
            {
                currentPlace: 'currentPlace',
                delete: true,
                deliveryDate: '25/07/2020',
                id: 1,
                manufacturingCompany: 'manufacturingCompany',
                manufacturingDate: '25/07/2019',
                ofnumber: 10,
                quantity: 1000,
                quantityDone: 100,
                update: false
            },
            {
                currentPlace: 'currentPlace',
                delete: true,
                deliveryDate: '25/07/2020',
                id: 2,
                manufacturingCompany: 'manufacturingCompany',
                manufacturingDate: '25/07/2019',
                ofnumber: 10,
                quantity: 1000,
                quantityDone: 100,
                update: false
            }
        ]

        const ofCustomerOrderItems = []
        for (const item of response)
            ofCustomerOrderItems.push(dispatch(
                'registerModule',
                {module: generateItem(item), path: ['ofCustomerOrderItems', item.id.toString()]},
                {root: true}
            ))
        await Promise.all(ofCustomerOrderItems)
    }
}

export type Actions = typeof actions
