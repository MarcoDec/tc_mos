import type {RootComputedGetters, State as RootState, ComputedGetters as VueComputedGetters} from '../..'
import type {FormOption} from '../../../types/bootstrap-5'
import type {State} from '.'
import {get} from 'lodash'

export declare type Getters = {
    children: (state: State, computed: ComputedGetters, rootState: RootState, rootGetters: RootComputedGetters) => string[]
    fullName: (state: State, computed: ComputedGetters, rootState: RootState, rootGetters: RootComputedGetters) => string
    hasChildren: (state: State, computed: ComputedGetters) => boolean
    label: (state: State) => string
    option: (state: State, computed: ComputedGetters) => FormOption
}

export declare type ComputedGetters = VueComputedGetters<Getters, State>

export const getters: Getters = {
    children(state, computed, rootState, rootGetters) {
        const children: string[] = []
        for (const item of (rootGetters[`${state.parentModuleName}/items`] as string[]))
            if (state['@id'] === (get(rootState, `${item}/parent`.split('/')) as string))
                children.push(get(rootState, `${item}/moduleName`.split('/')) as string)
        return children
    },
    fullName(state, computed, rootState, rootGetters) {
        if (typeof state.parent === 'string' && state.parent !== '0')
            for (const item of (rootGetters[`${state.parentModuleName}/items`] as string[]))
                if (state.parent === (get(rootState, `${item}/@id`.split('/')) as string))
                    return `${rootGetters[`${item}/fullName`] as string}/${state.name}`
        return state.name
    },
    hasChildren: (state, computed) => computed.children.length > 0,
    label: state => (typeof state.code === 'string' ? `${state.code} — ${state.name}` : state.name),
    option: (state, computed) => ({text: computed.fullName, value: state['@id']})
}
