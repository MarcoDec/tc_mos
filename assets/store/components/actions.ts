import type {State as RootState} from '..'
import type {State} from '.'
import type {State as Suppliers} from './component'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateComponent} from './component'

type ActionContext = VuexActionContext<State, RootState>

export const actions = {
    async fetchComponent({dispatch}: ActionContext): Promise<void> {
        const response: Suppliers[] = [
            {
                etat: 'refus',
                designation:'des',
                famille: 'cc',
                fournisseurs: 'cc',
                id: 1,
                img: 'ddd',
                indice: 'CAB-1000',
                nom: '3M FRANCE',
                ref: 'CAB-1000',
                show: true,
                traffic: false
            },
            {
                etat: 'attente',
                designation:'dess',
                famille: 'cc',
                fournisseurs: 'cc',
                id: 2,
                indice: 'CAB-1000',
                img: 'ddd',
                nom: 'ABB',
                ref: 'CAB-1000',
                show: true,
                traffic: true
            },
           
        ]

        const components = []
        for (const list of response)
        components.push(dispatch(
                'registerModule',
                {module: generateComponent(list), path: ['components', list.id.toString()]},
                {root: true}
            ))
        await Promise.all(components)
    }
}

export type Actions = typeof actions
