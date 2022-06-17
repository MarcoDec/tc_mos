import type {State} from '.'

export const mutations = {
    user(state: State, username: string): void {
        state.username = username
    }
}

export type Mutations = typeof mutations
