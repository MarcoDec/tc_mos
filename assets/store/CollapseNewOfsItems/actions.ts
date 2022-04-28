import type {DeepReadonly} from '../../types/types'
import type {State as Fields} from './CollapseNewOfsItem'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateField} from './CollapseNewOfsItem'

type ActionContext = DeepReadonly<VuexActionContext<State, RootState>>

export const actions = {
    async fetchItem({dispatch}: ActionContext): Promise<void> {
        const response: Fields[] = [
            {
                client: 'RENAULT',
                cmde: '3/2019',
                debutProd:'2018-12-31',
                finProd: '2019-01-07',
                id: 25,
                minDeLancement: 20,
                ofsAssocies: '',
                produit: '1318808X',
                qteDemandee: 56,
                quantite: 20,
                quantiteMin:30,
                siteDeProduction:'auto',
                etatInitialOF:'confirmé',
                lancerOF: false
            },
            {
                client: 'RENAULT',
                cmde: '4/2019',
                debutProd:'2019-12-31',
                finProd: '2029-01-07',
                id: 2,
                minDeLancement: 10,
                ofsAssocies: '',
                produit: '1318452X',
                qteDemandee: 60,
                quantite: 30,
                quantiteMin:10,
                siteDeProduction:'auto',
                etatInitialOF:'confirmé',
                lancerOF: false
            }
            ,
            {
                client: 'RENAULT',
                cmde: '4/2019',
                debutProd:'2011-12-31',
                finProd: '2022-12-07',
                id: 3,
                minDeLancement: 10,
                ofsAssocies: '',
                produit: '1318452X',
                qteDemandee: 60,
                quantite: 30,
                quantiteMin:31,
                siteDeProduction:'auto',
                etatInitialOF:'confirmé',
                lancerOF: false
            }
            
        ]

        const CollapseNewOfsItems = []
        for (const item of response)
        CollapseNewOfsItems.push(dispatch(
                'registerModule',
                {module: generateField(item), path: ['CollapseNewOfsItems', item.id.toString()]},
                {root: true}
            ))
        await Promise.all(CollapseNewOfsItems)
    }
}

export type Actions = typeof actions
