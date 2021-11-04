import type {ActionContext, ActionTree} from 'vuex'
import type {Mutations} from './mutations'
import type {RootState} from '../index'
import type {State} from './state'
import {UsersActionTypes} from './action-types'
import {UsersMutationTypes} from './mutation-types'

type AugmentedActionContext = Omit<ActionContext<State, RootState>, 'commit'> & {
    commit: <K extends keyof Mutations>(
        key: K,
        payload: Parameters<Mutations[K]>[1],
    ) => ReturnType<Mutations[K]>;
}

export interface Actions {
    [UsersActionTypes.FETCH_USERS]: (
        {commit}: AugmentedActionContext,
        username: string,
    ) => Promise<void>;
}

export const actions: ActionTree<State, RootState> = {
    async [UsersActionTypes.FETCH_USERS]({commit}, payload: {username: string, password: string}) {
        const response = await fetch(
            'http://localhost:8000/api/login',
            {
                body: JSON.stringify({
                    password: payload.password,
                    username: payload.username
                }),
                headers: {'Content-Type': 'application/json'},
                method: 'POST'
            }
        )
        const responseData = await response.json()
        console.log('res:', responseData)
        commit(UsersMutationTypes.SET_USER, responseData.username)
    }


}


