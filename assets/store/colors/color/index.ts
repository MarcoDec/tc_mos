import type {Module} from '../..'
import type {State} from './state'

export type {State}

export function generateColor(state: State): Module<State> {
    return {namespaced: true, state}
}
