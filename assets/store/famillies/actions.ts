import type {State as RootState} from '..'
import type {State} from '.'
import type {State as Famillies} from './familly'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateFamily} from './familly'

type ActionContext = VuexActionContext<State, RootState>


export const actions = {
   
    async getFamily({dispatch}: ActionContext): Promise<void> {
        const response : Famillies[] = [
            {
                id: 1,
                name: 'Cables',
            },
            {
                id: 2,
                name: 'Emballage',
            },
            {
                id: 3,
                name: 'Fixations',
            }
        ]

        const families = []
        for (const list of response)
        families.push(dispatch(
                'registerModule',
                {module: generateFamily(list), path: ['famillies', list.id.toString()]},
                {root: true}
            ))
        await Promise.all(families)
    }
}

export type Actions = typeof actions
