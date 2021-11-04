import type {RootState} from './index'
import type {ActionContext as VuexActionContext} from 'vuex'

export enum ActionTypes {
    FETCH_API = 'FETCH_API'
}

type ActionContext = VuexActionContext<RootState, RootState>

type FetchApi = {
    body: unknown
    method: 'DELETE' | 'GET' | 'OPTIONS' | 'PATCH' | 'POST' | 'PUT'
    route: string
}

type Actions = {
    [ActionTypes.FETCH_API]: (injectee: ActionContext, payload: FetchApi) => Promise<unknown>
}

export const actions: Actions = {
    async [ActionTypes.FETCH_API](injectee: ActionContext, {body, method, route}: FetchApi): Promise<unknown> {
        const response = await fetch(route, {
            body: JSON.stringify(body),
            headers: {'Content-Type': 'application/json'},
            method
        })
        const data = await response.json()
        return data
    }
}
