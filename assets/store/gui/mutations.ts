import type {State} from '.'

export enum MutationTypes {
    RESIZE = 'RESIZE'
}

export const mutations = {
    [MutationTypes.RESIZE](state: State): void {
        if (window.top !== null) {
            state.windowHeight = window.top.innerHeight
            state.windowWidth = window.top.innerWidth

        }
    }
}

export type Mutations = typeof mutations

