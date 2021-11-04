import {MutationTypes} from './mutations'
import type {RootState} from '../index'
import type {State} from './state'
import type {ActionContext as VuexActionContext} from 'vuex'

export enum ActionTypes {
    FETCH_USERS = 'FETCH_USERS'
}

type ActionContext = VuexActionContext<State, RootState>

type Login = {username: string | null, password: string | null}

export type Actions = {
    [ActionTypes.FETCH_USERS]: (injectee: ActionContext, payload: Login) => Promise<void>
}

export const actions: Actions = {
    async [ActionTypes.FETCH_USERS]({commit}: ActionContext, payload: Login): Promise<void> {
        const response = await fetch(
            'http://localhost:8000/api/login',
            {
                body: JSON.stringify({
                    password: payload.password,
                    username: payload.username
                }),
                headers: {'Content-Type': 'application/json'},
                method: 'POST'
            }
        )
        const responseData = await response.json()
        commit(MutationTypes.SET_USER, responseData.username)
    }
}
