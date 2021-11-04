import type {State} from './state'

export enum MutationTypes {
    SET_USER = 'SET_USER'
}

type Mutations = {
    [MutationTypes.SET_USER]: (state: State, username: string) => void
}

export const mutations: Mutations = {
    [MutationTypes.SET_USER](state: State, username: string): void {
        state.username = username
    }
}
