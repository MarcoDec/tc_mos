import type {State} from '.'
import * as Cookies from "../../cookie";

export enum MutationTypes {
    SET_USER = 'SET_USER',
    ERROR = 'ERROR',
    LOGOUT = 'LOGOUT',

}

export const mutations = {
    [MutationTypes.SET_USER](state: State, username: string): void {
        state.username = username
    },
    [MutationTypes.ERROR](state: State): void {
        state.error = true
    },
    [MutationTypes.LOGOUT](state: State): void {
        state.error = false
    },

}

export type Mutations = typeof mutations
