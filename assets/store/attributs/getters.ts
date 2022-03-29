import type {
    RootComputedGetters,
    State as RootState,
    ComputedGetters as VueComputedGetters
} from '..'
import type {FormField} from '../../types/bootstrap-5'
import type {State} from '.'

export declare type Getters = {
    fields: (
        state: State,
        computed: ComputedGetters,
        rootState: RootState,
        rootGetters: RootComputedGetters
    ) => FormField[] | string}

export declare type ComputedGetters = VueComputedGetters<Getters, State>

export const getters: Getters = {fields(state, computed, rootState, rootGetters) {
    const fields: FormField[] = []
    for (const field of Object.keys(state))
        fields.push(rootGetters[`attributs/${field}/field`] as FormField)
    return fields.sort((a, b) => a.name.localeCompare(b.name))
}}
