import type {State as Component} from './component'
import {generateModule} from './component'
import store from '../../index'

export enum ActionTypes {
    FETCH_COMP = 'FETCH_COMP'
}


export const actions = {
    async [ActionTypes.FETCH_COMP](): Promise<void> {

        const response: Component[] = [
            { name: 'comp1', '@id': '1', id: 1},
            { name: 'comp2', '@id':'2', id: 2},
            { name: 'comp3', '@id': '3', id: 3}
        ]
        for (const component of response)
            store.registerModule(['components', component.id.toString()], generateModule(component))
    }

}

export type Actions = typeof actions