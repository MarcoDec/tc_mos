import {LARGE_SCREEN} from '.'
import type {State} from '.'

export type Getters = {
    bottomHeight: (state: State, computed: GettersValues) => number
    bottomHeightPx: (state: State, computed: GettersValues) => string
    endWidth: (state: State, computed: GettersValues) => number
    endWidthPx: (state: State, computed: GettersValues) => string
    guiBottom: (state: State) => string
    heightPx: (state: State) => string
    innerBottomHeight: (state: State, computed: GettersValues) => number
    innerBottomHeightPx: (state: State, computed: GettersValues) => string
    innerHeight: (state: State) => number
    innerStartHeight: (state: State, computed: GettersValues) => number
    innerStartHeightPx: (state: State, computed: GettersValues) => string
    innerWidth: (state: State) => number
    innerWidthPx: (state: State, computed: GettersValues) => string
    marginEndPx: (state: State) => string
    marginTopPx: (state: State) => string
    marginedInnerHeight: (state: State, computed: GettersValues) => number
    marginedInnerWidth: (state: State, computed: GettersValues) => number
    paddingPx: (state: State) => string
    startWidth: (state: State, computed: GettersValues) => number
    startWidthPx: (state: State, computed: GettersValues) => string
    topHeight: (state: State, computed: GettersValues) => number
    topHeightPx: (state: State, computed: GettersValues) => string
    widthPx: (state: State) => string
}

export type GettersValues = {
    [key in keyof Getters]: ReturnType<Getters[key]>
}

const INNER_PADDING = 2
const MIDDLE = 2

export const getters: Getters = {
    bottomHeight: (state, computed) => Math.round(computed.marginedInnerHeight * state.ratio),
    bottomHeightPx: (state, computed) => `${computed.bottomHeight}px`,
    endWidth: (state, computed) => Math.round(computed.marginedInnerWidth - computed.startWidth),
    endWidthPx: (state, computed) => `${computed.endWidth}px`,
    guiBottom: state => (state.windowWidth >= LARGE_SCREEN ? 'AppShowGuiResizableCard' : 'AppShowGuiCard'),
    heightPx: state => `${state.height}px`,
    innerBottomHeight: (state, computed) => computed.bottomHeight - INNER_PADDING * state.padding,
    innerBottomHeightPx: (state, computed) => `${computed.innerBottomHeight}px`,
    innerHeight: state => Math.round(state.height - INNER_PADDING * state.padding),
    innerStartHeight: (state, computed) => computed.topHeight - INNER_PADDING * state.padding,
    innerStartHeightPx: (state, computed) => `${computed.innerStartHeight}px`,
    innerWidth: state => Math.round(state.width - INNER_PADDING * state.padding),
    innerWidthPx: (state, computed) => `${computed.innerWidth}px`,
    marginEndPx: state => `${state.marginEnd}px`,
    marginTopPx: state => `${state.marginTop}px`,
    marginedInnerHeight: (state, computed) => computed.innerHeight - state.marginTop,
    marginedInnerWidth: (state, computed) => computed.innerWidth - state.marginEnd,
    paddingPx: state => `${state.padding}px`,
    startWidth: (state, computed) => Math.round(computed.marginedInnerWidth / MIDDLE),
    startWidthPx: (state, computed) => `${computed.startWidth}px`,
    topHeight: (state, computed) => Math.round(computed.marginedInnerHeight - computed.bottomHeight),
    topHeightPx: (state, computed) => `${computed.topHeight}px`,
    widthPx: state => `${state.width}px`
}
