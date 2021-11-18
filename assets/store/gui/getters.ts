import type {State} from './state'
import {computed} from "vue";

export type Container = {
      containerHeight: number
      containerWidth: number
      bottomRatio: number
      heightBottom: number
      heightTop: number
      heightTopInner: number

}

export const getters = {
      containerHeight : (state: State) => ((state.windowHeight - 90) * state.freeSpace),
      containerWidth : (state: State) => (state.windowWidth - 5),
      bottomRatio : (state: State) => (1 - state.topRatio),
      heightBottom : (state: State, get:Container) => ((get.containerHeight - 12) * get.bottomRatio),
      heightBottomInner : (state: State, get:Container) => (get.heightBottom - 3),
      heightTop : (state: State, get:Container) => (get.containerHeight * state.topRatio),
      heightTopInner : (state: State, get:Container) => (get.heightTop - 10),

      heightBottompx : (state: State, get:Container) => (get.heightBottom+'px'),
      heightTopInnerpx : (state: State, get:Container) => (get.heightTopInner+'px'),

}
