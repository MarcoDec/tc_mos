import type {State} from './state'
import type {ComputedGetters as VueComputedGetters} from '..'

export const getters = {
    // eslint-disable-next-line  @typescript-eslint/no-unnecessary-condition
    hasNeeds: (state: State): boolean => state.needs.length > 0
}

export declare type Getters = typeof getters

export declare type ComputedGetters = VueComputedGetters<Getters, State>
