import * as Cookies from '../../cookie'
import type * as Store from '..'
import type {Mutations, State} from '.'

declare type ActionContext = Store.ActionContextTree<Actions, Mutations, State, Store.Actions>

declare type Login = {username: string | null, password: string | null}

declare type Actions = {
    login: (ctx: ActionContext, payload: Login) => Promise<void>
    logout: (ctx: ActionContext) => Promise<void>
}

export const actions: Actions = {
    async login({commit, dispatch}, {password, username}) {
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
    async logout({commit, dispatch}) {
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
        commit('login', {
            '@context': 'null',
            '@id': '0',
            '@type': 'null',
            id: 0,
            name: 'null',
            roles: [],
            token: 'null'
        })
    }
}
