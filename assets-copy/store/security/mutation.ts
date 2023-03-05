import type {State} from '.'

export enum MutationTypes {
    CODE = 'CODE',
    SET_USER = 'SET_USER',
    ERROR = 'ERROR',
    LOGOUT = 'LOGOUT',
    MSG_ERROR = 'MSG_ERROR',
    SHOW_MODAL = 'SHOW_MODAL'
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
    [MutationTypes.CODE](state: State, code: string): void {
        state.code = code
    },
    [MutationTypes.SHOW_MODAL](state: State): void {
        state.showModal = !state.showModal
    }
}

export type Mutations = typeof mutations
