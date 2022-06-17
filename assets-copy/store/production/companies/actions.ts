import type {State as Company} from './company'
import {generateModule} from './company'
import store from '../../index'

export enum ActionTypes {
    FETCH_COMPANY = 'FETCH_COMPANY'
}


export const actions = {
    async [ActionTypes.FETCH_COMPANY](): Promise<void> {

        const response: Company[] = [
            { name: 'company1', '@id': '1', id: 1},
            { name: 'company2', '@id':'2', id: 2},
            { name: 'company3', '@id': '3', id: 3}
        ]
        for (const company of response)
            store.registerModule(['companies', company.id.toString()], generateModule(company))
    }

}

export type Actions = typeof actions