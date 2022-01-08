import * as Cookies from '../../cookie'
import type {DeepReadonly} from '../../types/types'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {fetchApi} from '../../api'

type ActionContext = DeepReadonly<VuexActionContext<State, RootState>>

type Login = Readonly<{username: string | null, password: string | null}>

export const actions = {
    async login({commit, dispatch}: ActionContext, {password, username}: Login): Promise<void> {
        await dispatch('fetchApi', async () => {
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
                commit('user', user.username)
            }
        }, {root: true})
    },
    async logout({commit, dispatch}: ActionContext): Promise<void> {
        await dispatch('fetchApi', async () => {
            await fetchApi('/api/logout', {method: 'post'})
            Cookies.remove()
            commit('user', null)
        }, {root: true})
    }
}

export type Actions = typeof actions
