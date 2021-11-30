import type {State} from '.'
import {state} from "./state";

export const getters = {
    hasUser: (state: State): boolean => state.username !== null,
}

export type Getters = typeof getters
