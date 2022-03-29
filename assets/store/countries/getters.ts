import type {FormOption, FormOptions} from '../../types/bootstrap-5'
import type {
    RootComputedGetters,
    State as RootState,
    ComputedGetters as VueComputedGetters
} from '..'
import type {State} from '.'

export type Suppliers = {
    etat: string | null
    nom: string | null
    id: number | null
}

export declare type Getters = {
    options: (
        state: State,
        computed: ComputedGetters,
        rootState: RootState,
        rootGetters: RootComputedGetters
    ) => FormOptions
    phoneLabel: (state: State,
        computed: ComputedGetters,
        rootState: RootState,
        rootGetters: RootComputedGetters) => (code: string | null) => string | null
}
export declare type ComputedGetters = VueComputedGetters<Getters, State>

export const getters: Getters = {

    options(state, computed, rootState, rootGetters) {
        const options: FormOption[] = [{text: '', value: ''}]
        for (const country of Object.keys(state))
            options.push(rootGetters[`countries/${country}/option`] as FormOption)
        return options.sort((a, b) => a.text.localeCompare(b.text))
    },
    phoneLabel(state, computed, rootState, rootGetters){
        return (code: string | null): string | null => (code === null
            ? null
            : rootGetters[`countries/${code}/phoneLabel`] as string | null ?? null)
    }
}
