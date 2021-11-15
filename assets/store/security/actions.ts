import * as Cookies from '../../cookie'
import {MutationTypes} from '.'
import type {RootState} from '../index'
import type {State} from '.'
import {ActionTypes as StoreActionTypes} from '../actions'
import type {UserResponse} from '../../types/bootstrap-5'
import type {ActionContext as VuexActionContext} from 'vuex'

export enum ActionTypes {
    CONNECT = 'CONNECT',
    FETCH_USERS = 'FETCH_USERS',
    LOGOUT_USERS = 'LOGOUT_USERS'
}

type ActionContext = VuexActionContext<State, RootState>

type Login = {username: string | null, password: string | null}

export const actions = {
    async [ActionTypes.CONNECT]({commit, dispatch}: ActionContext): Promise<void> {
        if (Cookies.has('token') && Cookies.has('id')) {
            const users: UserResponse = await dispatch(
                StoreActionTypes.FETCH_API,
                {method: 'GET', route: `/api/employees/${Cookies.get('id')}`},
                {root: true}
            )
            commit(MutationTypes.SET_USER, users.username)
        }
    },
    async [ActionTypes.FETCH_USERS]({commit, dispatch}: ActionContext, {password, username}: Login): Promise<void> {
        const user: UserResponse = await dispatch(
            StoreActionTypes.FETCH_API,
            {body: {password, username}, method: 'POST', route: '/api/login'},
            {root: true}
        )
        Cookies.set('id', user.id)
        Cookies.set('token', user.token)
        commit(MutationTypes.SET_USER, user.username)
    },
    async [ActionTypes.LOGOUT_USERS]({commit, dispatch}: ActionContext): Promise<void> {
        await dispatch(
            StoreActionTypes.FETCH_API,
            {body: null, method: 'POST', route: '/api/logout'},
            {root: true}
        )
        Cookies.remove('id')
        Cookies.remove('token')
        commit(MutationTypes.SET_USER, null)
    }
}

export type Actions = typeof actions
