import type {State} from '.'
import type {Violation} from '../../../../types/types'

export const mutations = {
    violate(state: State, violations: Violation[] = []): void {
        state.violations = violations
    }
}

export declare type Mutations = typeof mutations
