import type {State as Country} from './country'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateCountry} from './country'

type ActionContext = VuexActionContext<State, RootState>

export const actions = {
    async fetchCountry({dispatch}: ActionContext): Promise<void> {
        const response: Country[] = [
            { 
                code: 'fr',
                name: 'France',
                prefix: '+333',
            },
            {
                code: 'tn',
                name: 'Tunisie',
                prefix: '+216',
            },
            {
                code: 'ch',
                name: 'Suisse',
                prefix: '+41',
            }
        ]

        const countries = []
        for (const list of response)
           countries.push(dispatch(
                'registerModule',
                {module: generateCountry(list), path: ['countries', list.code.toString()]},
                {root: true}
            ))
        await Promise.all(countries)
    }
}

export type Actions = typeof actions