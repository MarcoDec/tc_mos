import {LARGE_SCREEN} from '.'
import type {State} from '.'

const MARGIN_HEIGHT = 5
const MARGIN_WIDTH = 25
const RATIO_MIN = 0.1
const RATIO_MAX = 0.1

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
        if (ratio >= RATIO_MIN && ratio <= RATIO_MAX)
            state.ratio = ratio
    },
    [MutationTypes.RESIZE](state: State, el: HTMLDivElement): void {
        const rect = el.getBoundingClientRect()
        state.top = rect.top
        if (window.top !== null) {
            state.height = window.top.innerHeight - rect.top - MARGIN_HEIGHT
            state.width = window.top.innerWidth - MARGIN_WIDTH
            state.windowWidth = window.top.innerWidth
            if (state.windowWidth < LARGE_SCREEN)
                state.ratio = 0.5
        }
    }
}

export type Mutations = typeof mutations
