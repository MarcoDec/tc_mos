import type {State} from '.'
import type {Violation} from '../../../../../types/types'

export const mutations = {
    select(state: State, selected: boolean): void {
        state.selected = selected
    },
    toggle(state: State): void {
        if (state.id !== 0)
            state.opened = !state.opened
    },
    violate(state: State, violations: Violation[] = []): void {
        state.violations = violations
    }
}

export declare type Mutations = typeof mutations
