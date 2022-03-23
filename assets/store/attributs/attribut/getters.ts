import type {FormField, FormOption} from '../../../types/bootstrap-5'
import type {State} from '.'
import type {ComputedGetters as VueComputedGetters} from '../..'

export declare type Getters = {
    field: (state: State) => FormField
}

export declare type ComputedGetters = VueComputedGetters<Getters, State>

export const getters: Getters = {
    field: state => ({label: state.name, name: state.name}),
}
