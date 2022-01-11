import {StoreActionContext} from "../actions";
import type {State} from './notification'
import {generateModule} from './notification'

// import store from '..'
type ActionContext = StoreActionContext<State>
export enum ActionTypes {
    FETCH_NOTIF = 'FETCH_NOTIF'
}

export const actions = {
    async [ActionTypes.FETCH_NOTIF]({dispatch}:ActionContext): Promise<void> {
        const response: State[] = [
            {date: '', details: 'details1', id: 1, title: 'notif1', vu: false},
            {date: '', details: 'details2', id: 2, title: 'notif2', vu: true},
            {date: '', details: 'details3', id: 3, title: 'notif3', vu: false},
            {date: '', details: 'details4', id: 4, title: 'notif4', vu: false}
        ]
        const notifications = []
        for (const notification of response)
             notifications.push(dispatch(
                 'registerModule',
                 {module:generateModule(notification),path:['notifications', notification.id.toString()]},
                 {root:true}
             ))
        await Promise.all(notifications)
    }
}

export type Actions = typeof actions
