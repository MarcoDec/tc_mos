import type * as Store from '../..'
import type {FormOption} from '../../../types/bootstrap-5'
import type {State} from '.'

declare type Getters = {
    children: (state: State, computed: ComputedGetters) => string[]
    fullName: (state: State, computed: ComputedGetters) => string
    hasChildren: (state: State, computed: ComputedGetters) => boolean
    label: (state: State) => string
    option: (state: State, computed: ComputedGetters) => FormOption
}
declare type ComputedGetters = Store.ComputedGetters<Getters, State>

export const getters: Getters = {
    children: () => [],
    fullName: () => '',
    hasChildren: (state, computed) => computed.children.length > 0,
    label: state => (typeof state.code === 'string' ? `${state.code} — ${state.name}` : state.name),
    option: (state, computed) => ({text: computed.fullName, value: state['@id']})
}
