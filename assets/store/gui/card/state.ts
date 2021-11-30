export type State = {
    windowWidth: number
    windowheight: number
    freeSpaceUseRatio: number
    height: number
    topHeight: number
    onlyOne: boolean
    _currentRatio: number
    startY: number
    startHeight: number
    resizerEnabled: boolean
    loading: boolean
}

export const state: State = {
    windowWidth: window.innerWidth,
    windowheight: window.innerHeight,
    freeSpaceUseRatio: 0,
    height: 250,
    topHeight: 0,
    onlyOne: false,
    _currentRatio: 0,
    startY: 0,
    startHeight: 0,
    resizerEnabled: false,
    loading: false
}
