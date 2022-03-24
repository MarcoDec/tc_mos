import type {ComputedGetters, State} from '.'
import type {StoreActionContext} from '..'
import type {State as Items, Response} from './componentSupplier'
import {generateItem} from './componentSupplier'
import {generateItem as generatePrice} from '../componentSupplierPrices/componentSupplierPrice'



declare type ActionContext = StoreActionContext<State, ComputedGetters>

export const actions = {
    async fetchItem({dispatch}: ActionContext): Promise<void> {
        const response: Response[] = [
            {
                delete: true,
                proportion: 'aaaaaa',
                delai: 5,
                moq: 1,
                poidsCu: 'bbbbb',
                reference: 'ccc',
                indice: 1,
                prices:[{
                        delete: false,
                        price: 1000,
                        quantite:  100,
                        ref: 'afsfsfss',
                        id: 1,
                        update: true,
                        update2: false
                    },
                    {
                        delete: false,
                        price: 1000,
                        quantite:  30,
                        ref: 'azertsscssy',
                        id: 3,
                        update: true,
                        update2: false
                    }
                ],
                name: 'CAMION FR-MD',
                update: true,
                update2: false,
                id:1
            },
            {
                delete: true,
                proportion: 'vvvvvv',
                delai: 15,
                moq: 2,
                poidsCu: 'aaaa',
                reference: 'wwwww',
                indice: 2,
                prices:[
                    {
                        delete: false,
                        price: 100,
                        quantite:  50,
                        ref: 'azerty',
                        id: 2,
                        update: true,
                        update2: false
                    }
                ],
                name: 'CAMION',
                update: true,
                update2: false,
                id:2
            }
        ]

        const componentSuppliers = []
        for (let i = 0; i < response.length; i++){
            componentSuppliers.push(dispatch('registerModule', {...response[i], index: i + 1}))
        }
        await Promise.all(componentSuppliers)
    },
    async registerModule({dispatch}:ActionContext, item:Response): Promise<void> {
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
