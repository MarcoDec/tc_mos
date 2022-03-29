import type {State as FamillyProducts} from './famillyProduct'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateFamilyProduct} from './famillyProduct'

type ActionContext = VuexActionContext<State, RootState>

export const actions = {

    async getFamilyProduct({dispatch}: ActionContext): Promise<void> {
        const response: FamillyProducts[] = [
            {
                id: 1,
                name: 'FaisceauxPare-choc'
            },
            {
                id: 2,
                name: 'Fil'
            },
            {
                id: 3,
                name: 'Faisceaux'
            }
        ]

        const famillyProducts = []
        for (const list of response)
            famillyProducts.push(dispatch(
                'registerModule',
                {module: generateFamilyProduct(list), path: ['famillyProducts', list.id.toString()]},
                {root: true}
            ))
        await Promise.all(famillyProducts)
    }
}

export type Actions = typeof actions
