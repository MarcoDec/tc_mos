import type {State} from '.'

export enum MutationTypes {
    ENABLE_RESIZE = 'ENABLE_RESIZE',
    INIT_DRAG = 'INIT_DRAG',
    RATIO = 'RATIO'
}

export const mutations = {
    [MutationTypes.ENABLE_RESIZE](state: State): void {
        state.resizerEnabled = true
    },
    [MutationTypes.INIT_DRAG](state: State, {e, startHeight}: {e: MouseEvent, startHeight: number}): void {
        state.startHeight = startHeight
        state.startY = e.clientY
    },
    [MutationTypes.RATIO](state: State, ratio: number): void {
        if (ratio > 0.1 && ratio < 0.9)
            state.currentRatio = ratio
    }
}

export type Mutations = typeof mutations
