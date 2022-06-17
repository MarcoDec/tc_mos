import type {State} from '.'
const x = 0
export const getters = {
    all: (state: State): [string, number][] => Object.entries(state),
    count: (state: State): number => Object.values(state).reduce((sum, current) => current + sum, x)
}
export type Getters = typeof getters
