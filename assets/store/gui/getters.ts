import type {State} from '.'

export type Getters = {
    bottomHeight: (state: State, computed: GettersValues) => number
    bottomHeightPx: (state: State, computed: GettersValues) => string
    containerHeight: (state: State) => number
    containerHeightPx: (state: State, computed: GettersValues) => string
    containerWidth: (state: State) => number
    containerWidthPx: (state: State, computed: GettersValues) => string
    topHeight: (state: State, computed: GettersValues) => number
    topHeightPx: (state: State, computed: GettersValues) => string
    topInnerHeight: (state: State, computed: GettersValues) => number
    topInnerHeightPx: (state: State, computed: GettersValues) => string
}

type GettersValues = {
    [key in keyof Getters]: ReturnType<Getters[key]>
}

export const getters: Getters = {
    bottomHeight: (state, computed) => (computed.containerHeight - 12) * (state.onlyOne ? 1 : 1 - state.currentRatio),
    bottomHeightPx: (state, computed) => `${computed.bottomHeight}px`,
    containerHeight: state => state.visibleHeight - 110 + state.freeSpaceUseRatio,
    containerHeightPx: (state, computed) => `${computed.containerHeight}px`,
    containerWidth: state => state.visibleWidth - 30,
    containerWidthPx: (state, computed) => `${computed.containerWidth}px`,
    topHeight: (state, computed) => (state.onlyOne ? 0 : (computed.containerHeight - 20) * state.currentRatio),
    topHeightPx: (state, computed) => `${computed.topHeight}px`,
    topInnerHeight: (state, computed) => computed.topHeight - 10,
    topInnerHeightPx: (state, computed) => `${computed.topInnerHeight}px`
}
