import type {DeepReadonly} from '../../types/types'
import type {State as Fields} from './collapseOfsToConfirmItem'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateItem} from './collapseOfsToConfirmItem'

type ActionContext = DeepReadonly<VuexActionContext<State, RootState>>

export const actions = {
    async fetchToConfirmItem({dispatch}: ActionContext): Promise<void> {
        const response: Fields[] = [
            {
                client: 'RENAULT',
                cmde: '3/2019',
                confirmerOF: false,
                debutProd: '2018-12-31',
                finProd: '2019-01-07',
                id: 25,
                of: 137,
                produit: '1318808X',
                quantite: 20,
                quantiteProduite: 30,
                siteDeProduction: 'auto'
            },
            {
                client: 'RENAULT',
                cmde: '4/2019',
                confirmerOF: false,
                debutProd: '2019-12-31',
                finProd: '2020-01-07',
                id: 27,
                of: 140,
                produit: '1318666X',
                quantite: 20,
                quantiteProduite: 30,
                siteDeProduction: 'auto'
            }
        ]

        const collapseOfsToConfirmItems = []
        for (const item of response)
            collapseOfsToConfirmItems.push(dispatch(
                'registerModule',
                {module: generateItem(item), path: ['collapseOfsToConfirmItems', item.id.toString()]},
                {root: true}
            ))
        await Promise.all(collapseOfsToConfirmItems)
    }
}

export type Actions = typeof actions
