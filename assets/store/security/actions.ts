import * as Cookies from '../../cookie'
import type {ComputedGetters, State} from '.'
import type {StoreActionContext} from '..'

declare type ActionContext = StoreActionContext<State, ComputedGetters>

declare type Login = {username: string | null, password: string | null}

export const actions = {
    async login({commit, dispatch}: ActionContext, {password, username}: Login): Promise<void> {
        const user = await dispatch(
            'fetchApi',
            {
                body: {
                    password: password ?? '',
                    username: username ?? ''
                },
                method: 'post',
                url: '/api/login'
            },
            {root: true}
        )
        Cookies.set(user.id, user.token)
        commit('login', user)
    },
    async logout({commit, dispatch}: ActionContext): Promise<void> {
        await dispatch(
            'fetchApi',
            {
                body: {},
                method: 'post',
                url: '/api/logout'
            },
            {root: true}
        )
        Cookies.remove()
        commit('login', {})
    }
}

export declare type Actions = typeof actions
