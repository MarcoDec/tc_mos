import type {State} from '.'
import type {ComputedGetters as VueComputedGetters} from '..'

export declare type Getters = {
    has: (state: State) => (role: string) => boolean
    hasUser: (state: State) => boolean
    isPurchaseAdmin: (state: State, computed: ComputedGetters) => boolean
    isPurchaseReader: (state: State, computed: ComputedGetters) => boolean
    isPurchaseWriter: (state: State, computed: ComputedGetters) => boolean
}

export declare type ComputedGetters = VueComputedGetters<Getters, State>

export const getters: Getters = {
    has: state => role => state.roles?.includes(role) ?? false,
    hasUser: state => typeof state.username !== 'undefined',
    isPurchaseAdmin: (state, computed) => computed.has('ROLE_PURCHASE_ADMIN'),
    isPurchaseReader: (state, computed) => computed.isPurchaseAdmin || computed.has('ROLE_PURCHASE_READER'),
    isPurchaseWriter: (state, computed) => computed.isPurchaseAdmin || computed.has('ROLE_PURCHASE_WRITER')
}
