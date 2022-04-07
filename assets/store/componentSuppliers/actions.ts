import type {ComputedGetters, State} from '.'
import type {Response} from './componentSupplier'
import type {StoreActionContext} from '..'
import {generateItem} from './componentSupplier'
import {generateItem as generatePrice} from '../componentSupplierPrices/componentSupplierPrice'

declare type ActionContext = StoreActionContext<State, ComputedGetters>

export const actions = {
    async fetchItem({dispatch}: ActionContext): Promise<void> {
        const response: Response[] = [
            {
                delete: true,
                delai: 5,
                id: 1,
                indice: 1,
                moq: 1,
                name: 'CAMION FR-MD',
                poidsCu: 'bbbbb',
                prices: [{
                    delete: false,
                    price: 1000,
                    quantite: 100,
                    ref: 'afsfsfss',
                    id: 1,
                    update: true,
                    update2: false
                },
                {
                    delete: false,
                    price: 1000,
                    quantite: 30,
                    ref: 'azertsscssy',
                    id: 3,
                    update: true,
                    update2: false
                }],
                proportion: 'aaaaaa',
                reference: 'ccc',
                update: true,
                update2: false
            },
            {
                delete: true,
                delai: 15,
                id: 2,
                indice: 2,
                moq: 2,
                name: 'CAMION',
                poidsCu: 'aaaa',
                prices: [
                    {
                        delete: false,
                        price: 100,
                        quantite: 50,
                        ref: 'azerty',
                        id: 2,
                        update: true,
                        update2: false
                    }
                ],
                proportion: 'vvvvvv',
                reference: 'wwwww',
                update: true,
                update2: false
            }
        ]

        const componentSuppliers = []
        for (let i = 0; i < response.length; i++){
            componentSuppliers.push(dispatch('registerModule', {...response[i], index: i + 1}))
        }
        await Promise.all(componentSuppliers)
    },
    async registerModule({dispatch}: ActionContext, item: Response): Promise<void> {
        const componentSupplierPrices = []
        const prices = []
        for (let i = 0; i < item.prices.length; i++) {
            componentSupplierPrices.push(dispatch(
                'registerModule',
                {
                    module: generatePrice({...item.prices[i], index: i + 1}),
                    path: ['componentSupplierPrices', item.prices[i].id.toString()]
                },
                {root: true}
            ))
            prices.push(`componentSupplierPrices/${item.prices[i].id}`)
        }
        await Promise.all(componentSupplierPrices)
        await dispatch(
            'registerModule',
            {
                module: generateItem({...item, prices}),
                path: ['componentSuppliers', item.id.toString()]
            },
            {root: true}
        )
    }
}

export type Actions = typeof actions
