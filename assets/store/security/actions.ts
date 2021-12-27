import * as Cookies from '../../cookie'
import type {DeepReadonly} from '../../types/types'
import {MutationTypes} from '.'
import {ActionTypes as RootActionTypes} from '..'
import type {RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {fetchApi} from '../../api'

export enum ActionTypes {
    CONNECT = 'CONNECT',
    LOGIN = 'LOGIN',
    LOGOUT_USERS = 'LOGOUT_USERS'
}

type ActionContext = DeepReadonly<VuexActionContext<State, RootState>>

type Login = Readonly<{username: string | null, password: string | null}>

export const actions = {
    async [ActionTypes.CONNECT]({commit, dispatch}: ActionContext): Promise<void> {
        if (Cookies.has()) {
            const id = Cookies.get('id')
            if (typeof id !== 'undefined') {
                await dispatch(RootActionTypes.FETCH_API, async () => {
                    const user = await fetchApi('/api/employees/{id}', {params: {id}})
                    commit(MutationTypes.SET_USER, user.username)
                })
            }
        }
    },
    async [ActionTypes.LOGIN]({commit}: ActionContext, {password, username}: Login): Promise<void> {
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
            Cookies.set(user.id, user.token)
            commit(MutationTypes.SET_USER, user.username)
        }
    },
    async [ActionTypes.LOGOUT_USERS]({commit}: ActionContext): Promise<void> {
        await fetchApi('/api/logout', {method: 'post'})
        Cookies.remove()
        commit(MutationTypes.SET_USER, null)
    }
}

export type Actions = typeof actions
