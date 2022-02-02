import {LARGE_SCREEN} from '.'
import type {State} from '.'

export type Getters = {
    bottomHeight: (state: Readonly<State>, computed: GettersValues) => number
    bottomHeightPx: (state: Readonly<State>, computed: GettersValues) => string
    endWidth: (state: Readonly<State>, computed: GettersValues) => number
    endWidthPx: (state: Readonly<State>, computed: GettersValues) => string
    guiBottom: (state: Readonly<State>) => string
    heightPx: (state: Readonly<State>) => string
    innerBottomHeight: (state: Readonly<State>, computed: GettersValues) => number
    innerBottomHeightPx: (state: Readonly<State>, computed: GettersValues) => string
    innerHeight: (state: Readonly<State>) => number
    innerStartHeight: (state: Readonly<State>, computed: GettersValues) => number
    innerStartHeightPx: (state: Readonly<State>, computed: GettersValues) => string
    innerWidth: (state: Readonly<State>) => number
    innerWidthPx: (state: Readonly<State>, computed: GettersValues) => string
    marginEndPx: (state: Readonly<State>) => string
    marginTopPx: (state: Readonly<State>) => string
    marginedInnerHeight: (state: Readonly<State>, computed: GettersValues) => number
    marginedInnerWidth: (state: Readonly<State>, computed: GettersValues) => number
    paddingPx: (state: Readonly<State>) => string
    startWidth: (state: Readonly<State>, computed: GettersValues) => number
    startWidthPx: (state: Readonly<State>, computed: GettersValues) => string
    topHeight: (state: Readonly<State>, computed: GettersValues) => number
    topHeightPx: (state: Readonly<State>, computed: GettersValues) => string
    widthPx: (state: Readonly<State>) => string
}

export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
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
