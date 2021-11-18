import type {State} from './state'
import {onMounted, onUnmounted} from "vue";

export enum MutationTypes {
    GUI_SHOW = 'GUI_SHOW'
}

type Mutations = {
    [MutationTypes.GUI_SHOW]: (state: State) => void
}

export const mutations: Mutations = {
    [MutationTypes.GUI_SHOW](state: State): void {
        if (window.top !== null) {
           state.windowHeight = window.top.innerHeight
            state.windowWidth = window.top.innerWidth

        }
    }
}




