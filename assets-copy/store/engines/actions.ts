import type {State as Engine} from './engine'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateEngine} from './engine'

type ActionContext = VuexActionContext<State, RootState>

export const actions = {
    async fetchEngine({dispatch}: ActionContext): Promise<void> {
        const response: Engine[] = [
            {
                etat: 'refus',
                groupe: 'cc',
                id: 1,
                img: 'ddd',
                nom: '3M FRANCE',
                periode: 'CAB-1000',
                ref: 'CAB-1000',
                show: true,
                traffic: false
            },
            {
                etat: 'refus',
                groupe: 'cc',
                id: 2,
                img: 'ddd',
                nom: '3M FRANCE',
                periode: 'CAB-1000',
                ref: 'CAB-1000',
                show: true,
                traffic: false
            }

        ]

        const engines = []
        for (const list of response)
            engines.push(dispatch(
                'registerModule',
                {module: generateEngine(list), path: ['engines', list.id.toString()]},
                {root: true}
            ))
        await Promise.all(engines)
    }
}

export type Actions = typeof actions
