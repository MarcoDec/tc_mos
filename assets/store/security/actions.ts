import * as Cookies from '../../cookie'
import type {DeepReadonly} from '../../types/types'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import api from '../../api'

type ActionContext = DeepReadonly<VuexActionContext<State, RootState>>

type Login = Readonly<{username: string | null, password: string | null}>

export const actions = {
    async login({commit, dispatch}: ActionContext, {password, username}: Login): Promise<void> {
        await dispatch('fetchApi', async () => {
            const response = await api.path('/api/login').method('post').create()({
                password: password ?? '',
                username: username ?? ''
            })
            const user = response.data
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
            await api.path('/api/logout').method('post').create()({})
            Cookies.remove()
            commit('user', null)
        }, {root: true})
    }
}

export type Actions = typeof actions
