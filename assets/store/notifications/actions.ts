import Cookies from "js-cookie";
import {StoreActionContext} from "../actions";
import type {State} from './notification'
import {fetchApi} from "../../api";
import {generateModule, MutationTypes} from './notification'

type ActionContext = StoreActionContext<State>

export enum ActionTypes {
    FETCH_NOTIF = 'FETCH_NOTIF',
    NOTIF_READ = 'NOTIF_READ',
    DELETE_NOTIF = 'DELETE_NOTIF',
}

export const actions = {
    async [ActionTypes.FETCH_NOTIF]({commit, dispatch}: ActionContext): Promise<void> {
        const response = await fetchApi('/api/notifications',
            {method: 'get'},
        )
        console.log('lis--->', response['hydra:member'])
        const list: any = {}

        for (const li of response['hydra:member']) {
            if (typeof li['category'] !== 'undefined')
                list[li['category']] = {...li}
            console.log('sss--->', list[li['category']])

        }
        console.log('cccccc--->', list)

        const notifications = []
        for (const notification of response['hydra:member'])
            notifications.push(dispatch(
                    'registerModule',
                    {module: generateModule(notification), path: ['notifications', notification.id.toString()]},
                    {root: true}
                ),
            )
        await Promise.all(notifications)
    },
    async [ActionTypes.NOTIF_READ]({commit, dispatch}: ActionContext, payload: { id: string }): Promise<void> {
        const id = Cookies.get('idNotif')

        const response = await fetch('/api/notifications/' + id + '/read', {
            body: JSON.stringify({
                id: payload.id,
            }),
            method: 'patch',
            headers: {'Content-Type': 'application/json', "Authorization": 'Bearer ' + Cookies.get('token')}
        })
        console.log('readupatchaction--->', response)
    },
    async [ActionTypes.DELETE_NOTIF]({commit, dispatch}: ActionContext): Promise<void> {
        const id = Cookies.get('idNotif')

        const response = await fetch('/api/notifications/' + id, {
            method: 'delete',
            headers: {'Content-Type': 'application/json', "Authorization": 'Bearer ' + Cookies.get('token')}
        })
        console.log('deleteaction--->', response)
    }
}
export type Actions = typeof actions
