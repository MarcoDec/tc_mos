import type {State as Engine} from './engine'
import {generateModule} from './engine'
import store from '../../index'

export enum ActionTypes {
    FETCH_ENGINE = 'FETCH_ENGINE'
}


export const actions = {
    async [ActionTypes.FETCH_ENGINE](): Promise<void> {

        const response: Engine[] = [
            { name: 'engine1', '@id': '1', id: 1},
            { name: 'engine2', '@id':'2', id: 2},
            { name: 'engine3', '@id': '3', id: 3}
        ]
        for (const engine of response)
            store.registerModule(['engines', engine.id.toString()], generateModule(engine))
    }

}

export type Actions = typeof actions