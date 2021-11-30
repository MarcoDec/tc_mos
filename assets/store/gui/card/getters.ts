import type {State} from './state'

export type GettersCard = {
    visibleHeight: (state: State) => number
    visibleWidth: (state: State) => number
    containerWidth: (state: State) => number
    containerHeight: (state: State) => number
    onlyOne: (state: State) => boolean
    topHeight: (state: State, get: GettersValues) => number
    bottomHeight: (state: State, get: GettersValues) => number
    topCssVars: (state: State, get: GettersValues) => string
    heightBottomCssVars: (state: State, get: GettersValues) => string
    innerHeightBottomCssVars: (state: State, get: GettersValues) => string


}
type GettersValues = {
    [key in keyof GettersCard]: ReturnType<GettersCard[key]>
}

export const getters: GettersCard = {
    visibleHeight: state => state.windowheight,
    visibleWidth: state => state.windowWidth,
    containerWidth: state => state.windowWidth - 30,
    containerHeight: state => state.windowheight - 110 * state.freeSpaceUseRatio,
    topHeight: (state, get) => (state.onlyOne ? 0 : (get.containerHeight - 20) * state._currentRatio),
    bottomHeight: (state, get) => (state.onlyOne ? get.containerHeight - 12 : (get.containerHeight - 12) * (1 - state._currentRatio)),

    onlyOne: state => state.onlyOne,


    topCssVars: (state, get) => `${get.topHeight}px`,
    heightBottomCssVars: (state, get) => `${get.bottomHeight}px`, //bottomCssVars
    innerHeightBottomCssVars: (state, get) => `${state.height - 3}px` //bottomCssVars


}


