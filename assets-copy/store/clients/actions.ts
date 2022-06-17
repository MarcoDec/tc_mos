import type {State as Client} from './client'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateClient} from './client'

type ActionContext = VuexActionContext<State, RootState>

export const actions = {

    async getClients({dispatch}: ActionContext): Promise<void> {
        const response: Client[] = [
            {
                id: 1,
                name: 'LG'
            },
            {
                id: 2,
                name: 'OPEL'
            },
            {
                id: 3,
                name: 'AML'
            }
        ]

        const clients = []
        for (const list of response)
            clients.push(dispatch(
                'registerModule',
                {module: generateClient(list), path: ['clients', list.id.toString()]},
                {root: true}
            ))
        await Promise.all(clients)
    }
}

export type Actions = typeof actions
