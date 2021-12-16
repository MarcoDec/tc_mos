import type {State as Notification} from './notification'
import {generateModule} from './notification'
import store from '..'

export enum ActionTypes {
    FETCH_NOTIF = 'FETCH_NOTIF'
}


export const actions = {
    async [ActionTypes.FETCH_NOTIF](): Promise<void> {

        const response: Notification[] = [
            {date: '', details: 'details1', id: 1, title: 'notif1', vu: false},
            {date: '', details: 'details2', id: 2, title: 'notif2', vu: true},
            {date: '', details: 'details3', id: 3, title: 'notif3', vu: false},
            {date: '', details: 'details4', id: 4, title: 'notif4', vu: false}

        ]
        for (const notification of response)
            store.registerModule(['notifications', notification.id.toString()], generateModule(notification))
    }

}

export type Actions = typeof actions
