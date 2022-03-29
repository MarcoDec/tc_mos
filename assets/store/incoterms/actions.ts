import type {State as Incoterm} from './incoterm'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateIncoterm} from './incoterm'

type ActionContext = VuexActionContext<State, RootState>

export const actions = {
    async fetchIncoterms({dispatch}: ActionContext): Promise<void> {
        const response: Incoterm[] = [
            {
                id: 1,
                name: 'CFR'
            },
            {
                id: 2,
                name: 'CIF'
            },
            {
                id: 3,
                name: 'DAP'
            }
        ]

        const incoterms = []
        for (const list of response)
            incoterms.push(dispatch(
                'registerModule',
                {module: generateIncoterm(list), path: ['incoterms', list.id.toString()]},
                {root: true}
            ))
        await Promise.all(incoterms)
    }

}

export type Actions = typeof actions
