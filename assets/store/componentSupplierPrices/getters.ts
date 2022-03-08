import type {State} from '.'
import type {State as items} from './componentSupplierPrice'
import type {ComputedGetters as VueComputedGetters} from '..'


export type Getters = {
    items: (state: State) => items[] | null
}
export declare type ComputedGetters = VueComputedGetters<Getters, State>
export const getters: Getters = {
    items: state => Object.values(state)
}
