import type {FormOption} from '../../../types/bootstrap-5'
import type {State} from '.'
import type {ComputedGetters as VueComputedGetters} from '../..'

export declare type Getters = {
    option: (state: State, computed: ComputedGetters) => FormOption
}

export declare type ComputedGetters = VueComputedGetters<Getters, State>

export const getters: Getters = {
    option: state => ({text: state.ref, value: state.ref})
}
