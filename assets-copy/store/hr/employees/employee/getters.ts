import type {State} from './state'

export const getters = {
    toString:(state: State) => state.name
}

export type Getters = typeof getters