import type {FormOption, FormOptions} from '../../types/bootstrap-5'
import type {
    RootComputedGetters,
    State as RootState,
    ComputedGetters as VueComputedGetters
} from '..'
import type {State as Client} from './client'
import type {State} from '.'

export declare type Getters = {

    items: (state: State) => Client[] | null
    options: (
        state: State,
        computed: ComputedGetters,
        rootState: RootState,
        rootGetters: RootComputedGetters
    ) => FormOptions

}

export declare type ComputedGetters = VueComputedGetters<Getters, State>

export const getters: Getters = {

    items: state => Object.values(state),
    options(state, computed, rootState, rootGetters) {
        const options: FormOption[] = [{text: '', value: ''}]
        for (const client of Object.keys(state))
            options.push(rootGetters[`clients/${client}/option`] as FormOption)
        return options.sort((a, b) => a.text.localeCompare(b.text))
    }

}
