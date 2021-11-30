import type {State} from '.'

export enum MutationTypes {
    DRAG = 'DRAG',
    STARTY = 'STARTY',
    START_HEIGHT = 'START_HEIGHT',
    RESIZECLICK = 'RESIZECLICK',
    LOADING = 'LOADING',
    ONLYONE = 'ONLYONE'
}

export const mutations = {
    [MutationTypes.DRAG](state: State, newRatio: number): void {
        state._currentRatio = newRatio
    },
    [MutationTypes.STARTY](state: State, startY: number): void {
        state.startY = startY
    },
    [MutationTypes.START_HEIGHT](state: State, startX: number): void {
        state.startHeight = startX
    },
    [MutationTypes.RESIZECLICK](state: State): void {
        state.resizerEnabled = !state.resizerEnabled
        console.log('RESIZECLICK', state.resizerEnabled)
    },

    [MutationTypes.LOADING](state: State): void {
        state.loading = true
        console.log('LOADING', state.loading)
    },

    [MutationTypes.ONLYONE](state: State): void {
        console.log('ONLYONE', state.onlyOne)
    }

}

export type Mutations = typeof mutations

