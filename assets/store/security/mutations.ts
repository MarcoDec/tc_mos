import type {State} from '.'

export declare type Mutations = {
    login: (state: State, user: State) => void
}

export const mutations: Mutations = {
    login(state, user) {
        state.roles = user.roles
        state.username = user.username
    }
}
