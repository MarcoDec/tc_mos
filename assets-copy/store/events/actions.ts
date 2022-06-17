import type {State as Events} from './event'
import {generateModule} from './event'
import store from '../index'

export enum ActionTypes {
    FETCH_EVENTS = 'FETCH_EVENTS'
}
export const actions = {
    async [ActionTypes.FETCH_EVENTS](): Promise<void> {
        const response: Events[] = [
            {date: '2022-01-04', name: 'Event 1', id: 1, relationId: '1',relation: 'employees', type: 'x','@type': 'a'},
            {date: '2022-01-04', name: 'Event 2', id: 2, relationId: '1',relation: 'employees', type: 'y','@type': 'b'},
            {date: '2022-01-04', name: 'Event 3', id: 3, relationId: '2',relation: 'employees', type: 'y','@type': 'b'},
            {date: '2022-01-04', name: 'Event 4', id: 4, relationId: '2',relation: 'employees', type: 'y','@type': 'b'},
            {date: '2022-01-04', name: 'Event 5', id: 5, relationId: '1',relation: 'employees', type: 'y','@type': 'b'},
            {date: '2022-01-04', name: 'Event 6', id: 6, relationId: '1',relation: 'engines', type: 'y','@type': 'b'},
            {date: '2022-01-04', name: 'Event 7', id: 7, relationId: '1',relation: 'employees', type: 'y','@type': 'b'},
            {date: '2022-01-04', name: 'Event 8', id: 8, relationId: '3',relation: 'components', type: 'y','@type': 'b'},
            {date: '2022-10-20', name: 'Event 9', id: 9, relationId: '3',relation: 'employees', type: 'z','@type': 'c'},
            {date: '2021-09-19', name: 'Event 10', id: 10, relationId: '3',relation: 'companies', type: 'z','@type': 'c'},
            {date: '2021-08-18', name: 'Event 11', id: 11, relationId: '3',relation: 'customers', type: 'z','@type': 'c'}
        ]
        for (const event of response)
            store.registerModule(['events', event.id.toString()], generateModule(event))
    }
}

export type Actions = typeof actions