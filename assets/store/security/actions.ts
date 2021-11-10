import {MutationTypes} from './mutations'
import type {RootState} from '../index'
import type {State} from './state'
import {ActionTypes as StoreActionTypes} from '../actions'
import type {ActionContext as VuexActionContext} from 'vuex'
import Cookies from "js-cookie";
import router from "../../routing/router";

export enum ActionTypes {
    FETCH_USERS = 'FETCH_USERS',
    LOGOUT_USERS = 'LOGOUT_USERS',
    GET_USERS = 'GET_USERS'

}

type ActionContext = VuexActionContext<State, RootState>

type Login = { username: string | null, password: string | null }


export type Actions = {
    [ActionTypes.FETCH_USERS]: (injectee: ActionContext, payload: Login) => Promise<void>,
    [ActionTypes.LOGOUT_USERS]: (injectee: ActionContext) => Promise<void>,
    connect: (injectee: ActionContext) => Promise<void>,

}

export const actions: Actions = {
    async [ActionTypes.FETCH_USERS]({commit, dispatch}: ActionContext, {password, username}: Login): Promise<void> {
        const user = await dispatch(
            StoreActionTypes.FETCH_API,
            {body: {password, username}, method: 'POST', route: '/api/login'},
            {root: true}
        )
        console.log('response-->', user)
        await Cookies.set('token', user.token)
        await Cookies.set('id', user.id)

        const token = Cookies.get('token')

        commit(MutationTypes.SET_USER, user.username)
    },

    async [ActionTypes.LOGOUT_USERS]({commit, dispatch}: ActionContext): Promise<void> {
        const response = await dispatch(
            StoreActionTypes.FETCH_API,
            {body: null, method: 'POST', route: '/api/logout'},
            {root: true}
        )
        Cookies.remove('id')
        Cookies.remove('token')

        commit(MutationTypes.SET_USER, null)
    },

    async connect({commit, dispatch}: ActionContext): Promise<void> {

        if (document.cookie.indexOf('token') > -1 && document.cookie.indexOf('id') > -1) {
            const users = await dispatch(
                StoreActionTypes.FETCH_API,
                {method: 'GET', route: '/api/employees/' + Cookies.get('id')},
                {root: true}
            )
            console.log('connecter deja')
            commit(MutationTypes.SET_USER, users.username)

        }

    }
}
