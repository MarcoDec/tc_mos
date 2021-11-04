import {MutationTypes} from './mutations'
import type {RootState} from '../index'
import type {State} from './state'
import {ActionTypes as StoreActionTypes} from '../actions'
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
    async [ActionTypes.FETCH_USERS]({commit, dispatch}: ActionContext, {password, username}: Login): Promise<void> {
        const user = await dispatch(
            StoreActionTypes.FETCH_API,
            {body: {password, username}, method: 'POST', route: '/api/login'},
            {root: true}
        )
        commit(MutationTypes.SET_USER, user.username)
    }
}
