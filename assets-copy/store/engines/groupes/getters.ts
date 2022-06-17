import type {FormOption, FormOptions} from '../../../types/bootstrap-5'
import type {
    RootComputedGetters,
    State as RootState,
    ComputedGetters as VueComputedGetters
} from '../..'
import type {State} from '.'

export declare type Getters = {
    options: (
        state: State,
        computed: ComputedGetters,
        rootState: RootState,
        rootGetters: RootComputedGetters
    ) => FormOptions

}
export declare type ComputedGetters = VueComputedGetters<Getters, State>

export const getters: Getters = {

    options(state, computed, rootState, rootGetters) {
        const options: FormOption[] = [{text: '', value: ''}]
        for (const groupe of Object.keys(state))
            options.push(rootGetters[`groupes/${groupe}/option`] as FormOption)
        return options.sort((a, b) => a.text.localeCompare(b.text))
    }
}
