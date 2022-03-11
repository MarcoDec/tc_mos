import type {FormOption, FormOptions} from '../../types/bootstrap-5'
import type {RootComputedGetters, State as RootState, ComputedGetters as VueComputedGetters} from '..'
import type {State} from '.'
import {get} from 'lodash'

export declare type Getters = {
    items: (state: State) => string[]
    options: (state: State, computed: ComputedGetters, rootState: RootState, rootGetters: RootComputedGetters) => FormOptions
    selected: (state: State, computed: ComputedGetters, rootState: RootState) => string | null
}

export declare type ComputedGetters = VueComputedGetters<Getters, State>

export const getters: Getters = {
    items(state) {
        const items = []
        for (const item of Object.values(state))
            if (typeof item === 'object' && !Array.isArray(item))
                items.push(item.moduleName)
        return items
    },
    options(state, computed, rootState, rootGetters) {
        const options: FormOption[] = [{text: '', value: null}]
        for (const item of computed.items)
            options.push(rootGetters[`${item}/option`] as FormOption)
        return options.sort((a, b) => a.text.localeCompare(b.text))        
    },
    selected(state, computed, rootState) {
        for (const item of computed.items)
            if (get(rootState, `${item}/selected`.split('/')) as boolean)
                return item
        return null
    }
}
