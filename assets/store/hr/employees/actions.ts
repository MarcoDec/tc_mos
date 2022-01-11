import type {State as Empolyee} from './employee'
import {generateModule} from './employee'
import store from '../../index'

export enum ActionTypes {
    FETCH_EMP = 'FETCH_EMP'
}


export const actions = {
    async [ActionTypes.FETCH_EMP](): Promise<void> {

        const response: Empolyee[] = [
            { name: 'name1', '@id': '1', id: 1},
            { name: 'name2', '@id':'2', id: 2},
            { name: 'name3', '@id': '3', id: 3}
        ]
        for (const employee of response)
            store.registerModule(['employees', employee.id.toString()], generateModule(employee))
    }
}

export type Actions = typeof actions