import type {State as Compagnie} from './compagnie'
import type {State as RootState} from '../..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateCompagnie} from './compagnie'

type ActionContext = VuexActionContext<State, RootState>

export const actions = {
    async fetchCompagnie({dispatch}: ActionContext): Promise<void> {
        const response: Compagnie[] = [
            {
                id: 1,
                name: 'TCONCEPT'
            },
            {
                id: 2,
                name: 'TUNISIETCONCEPT'
            },
            {
                id: 3,
                name: 'MG2C'
            }
        ]

        const compagnies = []
        for (const list of response)
            compagnies.push(dispatch(
                'registerModule',
                {module: generateCompagnie(list), path: ['compagnies', list.id.toString()]},
                {root: true}
            ))
        await Promise.all(compagnies)
    }
}

export type Actions = typeof actions
