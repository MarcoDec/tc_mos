import type {MutationTree} from 'vuex'
import type {State} from './state'
import {UsersMutationTypes} from './mutation-types'

export type Mutations<S = State> = {
    [UsersMutationTypes.SET_USER]: (state: S, payload: string) => void;
}

export const mutations: Mutations & MutationTree<State> = {
    [UsersMutationTypes.SET_USER](state: State, username: string) {
        state.username = username
    }
}
