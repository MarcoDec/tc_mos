import type {State} from '.'

export enum MutationTypes {
    RESIZE = 'RESIZE'
}

export const mutations = {
    [MutationTypes.RESIZE](state: State, el: HTMLDivElement): void {
        const rect = el.getBoundingClientRect()
        if (window.top !== null) {
            state.height = window.top.innerHeight - rect.top - 5
            state.width = window.top.innerWidth - 25
        }
    }
}

export type Mutations = typeof mutations
