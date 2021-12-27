import type {State} from '.'

export enum MutationTypes {
    SET_USER = 'SET_USER'
}

export const mutations = {
    [MutationTypes.SET_USER](state: State, username: string): void {
        state.username = username
    }
}

export type Mutations = typeof mutations
