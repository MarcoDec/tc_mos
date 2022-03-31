import type * as Store from '..'
import type {RootState, State} from '.'
import type {FormOptions} from '../../types/bootstrap-5'

declare type Getters = {
    items: (state: State) => string[]
    options: (state: State, computed: ComputedGetters) => FormOptions
    selected: (state: State, computed: ComputedGetters, rootState: RootState) => string | null
}
declare type ComputedGetters = Store.ComputedGetters<Getters, State, RootState>

export const getters: Getters = {
    items(state) {
        const items = []
        for (const item of Object.values(state))
            if (typeof item === 'object' && !Array.isArray(item))
                items.push(item.moduleName)
        return items
    },
    options: () => [],
    selected: () => null
}
