import Cookies from "js-cookie";
import {StoreActionContext} from "../../actions";
import type {State} from './setting'
import {generateModule, MutationTypes} from './setting'

type ActionContext = StoreActionContext<State>

export enum ActionTypes {
    FETCH_SETTING = 'FETCH_SETTING',
    NOTIF_READ = 'NOTIF_READ',
    DELETE_NOTIF = 'DELETE_NOTIF',
}

export const actions = {

    async [ActionTypes.FETCH_SETTING]({commit, dispatch}: ActionContext): Promise<void> {
        const response  =  [
            {
                delete: true,
                name: 'bbb',
                valeur: 'vvv',
                update: true
            },
            {
                delete: true,
                name: 'aaaa',
                valeur: 'xxxx',
                update: true
            },
        ]
        commit(MutationTypes.LIST,response)
    },

}
export type Actions = typeof actions
