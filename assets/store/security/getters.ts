import type {State} from '.'

export const getters = {
    hasUser: (state: State): boolean => state.username !== null
}

export type Getters = typeof getters
