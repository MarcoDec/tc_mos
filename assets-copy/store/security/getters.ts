import type {State} from '.'
import type {ComputedGetters as VueComputedGetters} from '..'

export const getters = {
    hasUser: (state: State): boolean => state.username !== null
}

export declare type Getters = typeof getters

export declare type ComputedGetters = VueComputedGetters<Getters, State>
