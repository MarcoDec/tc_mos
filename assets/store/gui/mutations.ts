import type {State} from '.'

export enum MutationTypes {
    DISABLE_DRAG = 'DISABLE_DRAG',
    ENABLE_DRAG = 'ENABLE_DRAG',
    RATIO = 'RATIO',
    RESIZE = 'RESIZE'
}

export const mutations = {
    [MutationTypes.DISABLE_DRAG](state: State): void {
        state.drag = false
    },
    [MutationTypes.ENABLE_DRAG](state: State): void {
        state.drag = true
    },
    [MutationTypes.RATIO](state: State, ratio: number): void {
        if (ratio >= 0.1 && ratio <= 0.9)
            state.ratio = ratio
    },
    [MutationTypes.RESIZE](state: State, el: HTMLDivElement): void {
        const rect = el.getBoundingClientRect()
        state.top = rect.top
        if (window.top !== null) {
            state.height = window.top.innerHeight - rect.top - 5
            state.width = window.top.innerWidth - 25
            state.windowWidth = window.top.innerWidth
        }
    }
}

export type Mutations = typeof mutations
