import type {State} from '.'
import type {ComputedGetters as VueComputedGetters} from '..'

export const getters = {
    hasNeeds: state => state.needs.length > 0
}

export declare type Getters = typeof getters

export declare type ComputedGetters = VueComputedGetters<Getters, State>
