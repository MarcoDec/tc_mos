import type {State} from '.'

export const mutations = {
    select(state: State, selected: boolean): void {
        state.selected = selected
    },
    toggle(state: State): void {
        if (state.id !== 0)
            state.opened = !state.opened
    }
}

export declare type Mutations = typeof mutations
