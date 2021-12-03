import type {State} from '.'

export const getters = {
    height: (state: State): string => `${state.height}px`,
    width: (state: State): string => `${state.width}px`
}

export type Getters = typeof getters
