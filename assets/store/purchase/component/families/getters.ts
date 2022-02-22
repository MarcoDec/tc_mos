import type {FormOption, FormOptions} from '../../../../types/bootstrap-5'
import type {RootComputedGetters, State as RootState, ComputedGetters as VueComputedGetters} from '../../..'
import type {State} from '.'
import {get} from 'lodash'

export declare type Getters = {
    families: (state: State) => string[]
    options: (state: State, computed: ComputedGetters, rootState: RootState, rootGetters: RootComputedGetters) => FormOptions
    selected: (state: State, computed: ComputedGetters, rootState: RootState) => string | null
}

export declare type ComputedGetters = VueComputedGetters<Getters, State>

export const getters: Getters = {
    families(state) {
        const families = []
        for (const family of Object.values(state))
            if (typeof family === 'object' && !Array.isArray(family))
                families.push(family.moduleName)
        return families
    },
    options(state, computed, rootState, rootGetters) {
        const options: FormOption[] = [{text: '', value: null}]
        for (const family of computed.families)
            options.push(rootGetters[`${family}/option`] as FormOption)
        return options.sort((a, b) => a.text.localeCompare(b.text))
    },
    selected(state, computed, rootState) {
        for (const family of computed.families)
            if (get(rootState, `${family}/selected`.split('/')) as boolean)
                return family
        return null
    }
}
