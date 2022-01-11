import type {ReadGetters as RootGetters, ReadState as RootState} from '../../..'
import type {DeepReadonly} from '../../../../types/types'
import type {ReadState} from '.'
import type {TreeItem} from '../../../../types/tree'

export type Getters = {
    children: (state: ReadState) => (id: string) => number[]
    families: (state: ReadState, computed: GettersValues, rootState: RootState, rootGetters: RootGetters) => number[]
    tree: (state: ReadState, computed: GettersValues, rootState: RootState, rootGetters: RootGetters) => TreeItem
}

type GettersValues = DeepReadonly<{[key in keyof Getters]: ReturnType<Getters[key]>}>

export const getters: Getters = {
    children: state => (id: string): number[] => {
        const children: number[] = []
        for (const family of Object.values(state))
            if (family.parent === id)
                children.push(family.id)
        return children
    },
    families: (state, computed, rootState, rootGetters) => {
        const families: number[] = []
        for (const family of Object.values(state))
            if (rootGetters[`families/${family.id}/root`] as boolean)
                families.push(family.id)
        return families
    },
    tree: (state, computed, rootState, rootGetters) => ({
        children: computed.families.map(family => rootGetters[`families/${family}/tree`] as TreeItem),
        id: 0,
        label: 'Familles'
    })
}
