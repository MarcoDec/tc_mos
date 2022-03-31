import type {State} from '.'

export const mutations = {
    login(state: State, user: State): void {
        state.roles = user.roles
        state.username = user.username
    }
}

export declare type Mutations = typeof mutations
