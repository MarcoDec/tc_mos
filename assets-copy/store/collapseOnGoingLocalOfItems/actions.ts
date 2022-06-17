import type {DeepReadonly} from '../../types/types'
import type {State as Fields} from './collapseOnGoingLocalOfItem'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateItem} from './collapseOnGoingLocalOfItem'

type ActionContext = DeepReadonly<VuexActionContext<State, RootState>>

export const actions = {
    async fetchLocalOfItem({dispatch}: ActionContext): Promise<void> {
        const response: Fields[] = [
            {
                client: 'RENAULT',
                cmde: '3/2019',
                debutProd: '2018-12-31',
                etat: 'confirmed',
                finProd: '2019-01-07',
                id: 25,
                of: 137,
                produit: '1318808X',
                quantite: 20,
                quantiteProduite: 30
            },
            {client: 'RENAULT',
                cmde: '4/2019',
                debutProd: '2019-12-31',
                etat: 'blocked',
                finProd: '2020-01-07',
                id: 27,
                of: 140,
                produit: '1318666X',
                quantite: 20,
                quantiteProduite: 30}
        ]

        const collapseOnGoingLocalOfItems = []
        for (const item of response)
            collapseOnGoingLocalOfItems.push(dispatch(
                'registerModule',
                {module: generateItem(item), path: ['collapseOnGoingLocalOfItems', item.id.toString()]},
                {root: true}
            ))
        await Promise.all(collapseOnGoingLocalOfItems)
    }
}

export type Actions = typeof actions
