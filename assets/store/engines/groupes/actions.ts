import type {State as Groupes} from './groupe'
import type {State as RootState} from '../..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateGroupe} from './groupe'

type ActionContext = VuexActionContext<State, RootState>

export const actions = {
    async fetchGroupe({dispatch}: ActionContext): Promise<void> {
        const response: Groupes[] = [
            {
                id: 1,
                name: 'Machine'
            },
            {
                id: 2,
                name: 'Moule'
            },
            {
                id: 3,
                name: 'Equipements de sécurité'
            }
        ]

        const groupes = []
        for (const list of response)
            groupes.push(dispatch(
                'registerModule',
                {module: generateGroupe(list), path: ['groupes', list.id.toString()]},
                {root: true}
            ))
        await Promise.all(groupes)
    }
}

export type Actions = typeof actions
