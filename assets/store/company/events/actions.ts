import type {State as Events} from './event'
import {generateModule} from './event'
import store from '../../index'

export enum ActionTypes {
    FETCH_EVENTS = 'FETCH_EVENTS'
}


export const actions = {
    async [ActionTypes.FETCH_EVENTS](): Promise<void> {

        const response: Events[] = [
            {date: '2021-12-22', name: 'name1', id: 1, relationId: 'relationId', type: ['x']},
            {date: '2021-12-21', name: 'name1', id: 2, relationId: 'relationId', type: ['y']},
            {date: '2021-12-20', name: 'name1', id: 3, relationId: 'relationId', type: ['z']},
        ]
        const event = response.map(eventnew => {return eventnew.date});
        console.log('dateevent',event)
        for (const event of response)
            store.registerModule(['events', event.id.toString()], generateModule(event))
    }

}

export type Actions = typeof actions