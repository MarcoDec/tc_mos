import type {DeepReadonly} from '../../types/types'
import type {State as Items} from './facturesCustomerOrderItem'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateItem} from './facturesCustomerOrderItem'

type ActionContext = DeepReadonly<VuexActionContext<State, RootState>>

export const actions = {
    async fetchItem({dispatch}: ActionContext): Promise<void> {
        const response: Items[] = [
            {
                currentPlace: 'currentPlace',
                deadlineDate: '04/07/2019',
                delete: true,
                id: 1,
                invoiceDate: '04/07/2019',
                invoiceNumber: 'invoiceNumber',
                invoiceSendByEmail: 'invoiceSendByEmail',
                totalHT: 'totalHT',
                totalTTC: 'totalTTC',
                update: false,
                vta: 'Vta'
            },
            {
                currentPlace: 'currentPlace',
                deadlineDate: '04/07/2019',
                delete: true,
                id: 2,
                invoiceDate: '04/07/2019',
                invoiceNumber: 'invoiceNumberrrr',
                invoiceSendByEmail: 'invoiceSendByEmaillll',
                totalHT: 'totalHTt',
                totalTTC: 'totalTTCc',
                update: false,
                vta: 'Vta'
            }
        ]

        const facturesCustomerOrderItems = []
        for (const item of response)
            facturesCustomerOrderItems.push(dispatch(
                'registerModule',
                {module: generateItem(item), path: ['facturesCustomerOrderItems', item.id.toString()]},
                {root: true}
            ))
        await Promise.all(facturesCustomerOrderItems)
    }
}
export type Actions = typeof actions
