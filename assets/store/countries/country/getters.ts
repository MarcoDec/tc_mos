import type {ComputedGetters as VueComputedGetters} from '../..'
import type {FormOption} from '../../../types/bootstrap-5'
import type {State} from '.'
import { S } from 'ts-toolbelt'

export declare type Getters = {
    option: (state: State, computed: ComputedGetters) => FormOption
    phoneLabel: (state: State, computed: ComputedGetters) => string

}

export declare type ComputedGetters = VueComputedGetters<Getters, State>

export const getters: Getters = { 
    option: (state) => ({text: state.name, value: state['code']}),
    phoneLabel : (state) => `${state.name} ${state.prefix}` 
}
