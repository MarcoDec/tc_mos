import type {State} from '.'

export type Getters = {
    bottomHeight: (state: State, computed: GettersValues) => number
    containerHeight: (state: State) => number
    containerHeightPx: (state: State, computed: GettersValues) => string
    containerWidth: (state: State) => number
    containerWidthPx: (state: State, computed: GettersValues) => string
    topHeight: (state: State, computed: GettersValues) => number
}

type GettersValues = {
    [key in keyof Getters]: ReturnType<Getters[key]>
}

export const getters: Getters = {
    bottomHeight: (state, computed) => (computed.containerHeight - 12) * (state.onlyOne ? 1 : 1 - state.currentRatio),
    containerHeight: state => state.visibleHeight - 110 + state.freeSpaceUseRatio,
    containerHeightPx: (state, computed) => `${computed.containerHeight}px`,
    containerWidth: state => state.visibleWidth - 30,
    containerWidthPx: (state, computed) => `${computed.containerWidth}px`,
    topHeight: (state, computed) => (state.onlyOne ? 0 : (computed.containerHeight - 20) * state.currentRatio)
}
