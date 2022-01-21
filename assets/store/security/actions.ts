import * as Cookies from '../../cookie'
import type {State} from '.'
import type {StoreActionContext} from '..'

type ActionContext = StoreActionContext<State>

type Login = Readonly<{username: string | null, password: string | null}>

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
                multipart: false,
                url: '/api/login'
            },
            {root: true}
        )
        if (
            typeof user.id !== 'undefined'
            && typeof user.token !== 'undefined'
            && user.token !== null
            && typeof user.username !== 'undefined'
        ) {
            Cookies.set(user.id, user.token)
            commit('user', user.username)
        }
    },
    async logout({commit, dispatch}: ActionContext): Promise<void> {
        await dispatch(
            'fetchApi',
            {
                body: {},
                method: 'post',
                multipart: false,
                url: '/api/logout'
            },
            {root: true}
        )
        Cookies.remove()
        commit('user', null)
    }
}

export type Actions = typeof actions
