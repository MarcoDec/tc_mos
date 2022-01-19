import type {ReadGetters as RootGetters, ReadState as RootState} from '../../../..'
import type {DeepReadonly} from '../../../../../types/types'
import type {State} from '.'
import type {TreeItem} from '../../../../../types/tree'

export type Getters = {
    children: (state: State, computed: GettersValues, rootState: RootState, rootGetters: RootGetters) => number[]
    childrenTree: (state: State, computed: GettersValues, rootState: RootState, rootGetters: RootGetters) => TreeItem[]
    fullName: (state: State, computed: GettersValues, rootState: RootState, rootGetters: RootGetters) => string
    root: (state: State) => boolean
    tree: (state: State, computed: GettersValues) => TreeItem
}

type GettersValues = DeepReadonly<{[key in keyof Getters]: ReturnType<Getters[key]>}>

export const getters: Getters = {
    children: (state, computed, rootState, rootGetters) =>
        (rootGetters['families/children'] as (id: string) => number[])(state['@id']),
    childrenTree: (state, computed, rootState, rootGetters) =>
        computed.children.map(child => rootGetters[`families/${child}/tree`] as TreeItem),
    fullName(state, computed, rootState, rootGetters) {
        if (typeof state.parent !== 'undefined' && state.parent !== null) {
            const parentFamily = (rootGetters['families/fullName'] as (id: string) => string | null)(state.parent)
            if (parentFamily !== null)
                return `${parentFamily}/${state.name}`
        }
        return state.name
    },
    root: state => typeof state.parent === 'undefined' || state.parent === null,
    tree: (state, computed) => ({children: computed.childrenTree as TreeItem[], id: state.id, label: state.name})
}
