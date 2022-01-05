/*eslint linebreak-style: ["error", "windows"]*/
import type {State} from '.'
const month = 0
export const getters = {
    all: (state: State): [string, number][] => Object.entries(state),
    count: (state: State): number => Object.values(state).reduce((sum, current) => current + sum, month)
}
export type Getters = typeof getters
