import * as Cookies from '../../cookie'
import type {DeepReadonly} from '../../types/types'
import {MutationTypes} from '.'
import type {RootState} from '../index'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {fetchApi} from '../../api'

export enum ActionTypes {
    CONNECT = 'CONNECT',
    FETCH_USERS = 'FETCH_USERS',
    LOGOUT_USERS = 'LOGOUT_USERS'
}

type ActionContext = DeepReadonly<VuexActionContext<State, RootState>>

type Login = Readonly<{username: string | null, password: string | null}>

export const actions = {
    async [ActionTypes.CONNECT]({commit}: ActionContext): Promise<void> {
        if (Cookies.has('token') && Cookies.has('id')) {
            const id = Cookies.get('id')
            if (typeof id === 'undefined')
                return
            const user = await fetchApi('/api/employees/{id}', {params: {id}})
            commit(MutationTypes.SET_USER, user.username)
        }
    },
    async [ActionTypes.FETCH_USERS]({commit}: ActionContext, {password, username}: Login): Promise<void> {
        try {
            const user = await fetchApi('/api/login', {
                json: {password: password ?? '', username: username ?? ''},
                method: 'post'
            })
            if (
                typeof user.id !== 'undefined'
                && typeof user.token !== 'undefined'
                && user.token !== null
                && typeof user.username !== 'undefined'
            ) {
                Cookies.set('id', user.id.toString())
                Cookies.set('token', user.token)
                commit(MutationTypes.SET_USER, user.username)
            }
        } catch (error: unknown) {
            if (error instanceof Response) {
                const response = await error.json() as string | {'hydra:description': string, 'hydra:title': string}
                const message = typeof response === 'string' ? response : `${response['hydra:title']} : ${response['hydra:description']}`
                commit(MutationTypes.MSG_ERROR, message)
                commit(MutationTypes.CODE, error.status)
                commit(MutationTypes.ERROR, true)
            }
        }
    },
    async [ActionTypes.LOGOUT_USERS]({commit}: ActionContext): Promise<void> {
        await fetchApi('/api/logout', {method: 'post'})
        Cookies.remove('id')
        Cookies.remove('token')
        commit(MutationTypes.SET_USER, null)
    }
}

export type Actions = typeof actions
