import type {State} from '.'

export const mutations = {
    user(state: State, username: string): void {
        state.username = username
    }
}

export declare type Mutations = typeof mutations
