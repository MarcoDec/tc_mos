import type {State} from '.'

export enum MutationTypes {
    SET_USER = 'SET_USER',
    ERROR = 'ERROR',
    LOGOUT = 'LOGOUT',
    MSG_ERROR = 'MSG_ERROR',
    STATUS = 'STATUS'

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
    [MutationTypes.MSG_ERROR](state: State, msg: string): void {
        state.msgError = msg
    },
    [MutationTypes.STATUS](state: State, status: string): void {
        state.status = status
    }

}

export type Mutations = typeof mutations
