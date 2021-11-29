import type {State} from './state'

export type Getters = {
      containerHeight: (state: State) => number
      containerWidth: (state: State) => number
      bottomRatio: (state: State) => number
      heightBottom: (state: State,get:GettersValues) => number
      heightBottomInner: (state: State,get:GettersValues) => number
      heightTop: (state: State,get:GettersValues) => number
      heightTopInner: (state: State,get:GettersValues) => number
      maxInnerHeight: (state: State,get:GettersValues) => number
      heightInner: (state: State,get:GettersValues) => number
      tabContentInner: (state: State,get:GettersValues) => number
      heightBottompx: (state: State,get:GettersValues) => string
      heightTopInnerpx: (state: State,get:GettersValues) => string
      heightBottomInnerpx: (state: State,get:GettersValues) => string
      tabHeightpx: (state: State,get:GettersValues) => string
      innerHeightpx: (state: State,get:GettersValues) => string
      maxInnerHeightpx: (state: State,get:GettersValues) => string
      tabContentHeightpx: (state: State,get:GettersValues) => string

}
type GettersValues = {
      [key in keyof Getters] : ReturnType<Getters[key]>
}

export const getters: Getters = {
      containerHeight : state => ((state.windowHeight - 90) * state.freeSpace),
      containerWidth : state => (state.windowWidth - 5),
      bottomRatio : state => (1 - state.topRatio),
      heightBottom : (state, get) => ((get.containerHeight - 12) * get.bottomRatio),
      heightBottomInner : (state, get) => (get.heightBottom - 3),
      heightTop : (state, get) => (get.containerHeight * state.topRatio),
      heightTopInner : (state, get) => (get.heightTop - 10),

      maxInnerHeight : (state, get) => (get.heightBottom - 10),
      heightInner : (state, get) => (get.heightBottom - 20),
      tabContentInner : (state, get) => (get.heightInner - 60),

      heightBottompx : (state, get) => (get.heightBottom+'px'),
      heightTopInnerpx : (state, get) => (get.heightTopInner+'px'),
      heightBottomInnerpx : (state, get) => (get.heightBottomInner+'px'),

      tabHeightpx : (state, get) => (get.heightBottom+'px'),
      innerHeightpx : (state, get) => (get.heightInner+'px'),
      maxInnerHeightpx : (state, get) => (get.maxInnerHeight+'px'),
      tabContentHeightpx : (state, get) => (get.tabContentInner+'px'),

}


