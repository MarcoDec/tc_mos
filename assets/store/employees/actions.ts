import type {State as Employee} from './employee'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateEmployee} from './employee'

type ActionContext = VuexActionContext<State, RootState>

export const actions = {
    async fetchEmployee({dispatch}: ActionContext): Promise<void> {
        const response: Employee[] = [
            {
                compte: true,
                etat: 'valide',
                id: 1,
                identifiant: 'super',
                initiale: 'SU',
                matricule: '26',
                nom: 'user',
                prenom: 'super',
                service: 'cc',
                show: true,
                traffic: true
            },
            {
                compte: true,
                etat: 'valide',
                id: 2,
                identifiant: 'admin',
                initiale: 'AD',
                matricule: '28',
                nom: 'admin',
                prenom: 'super',
                service: 'cc',
                show: true,
                traffic: true
            }
        ]

        const employees = []
        for (const list of response)
            employees.push(
                dispatch(
                    'registerModule',
                    {
                        module: generateEmployee(list),
                        path: ['employees', list.id.toString()]
                    },
                    {root: true}
                )
            )
        await Promise.all(employees)
    }
}

export type Actions = typeof actions
