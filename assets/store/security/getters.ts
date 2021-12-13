import type {State} from '.'

export const getters = {hasUser: (state: Readonly<State>): boolean => state.username !== null}

export type Getters = typeof getters
