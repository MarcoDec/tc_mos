const INNER_PADDING = 2
const LARGE_SCREEN = 1140

export const gui = {
    actions: {
        async drag({commit, getters, state}) {
            function drag({y}) {
                commit('ratio', (getters.innerHeight - y + state.marginTop) / getters.innerHeight)
            }

            function stopDrag() {
                commit('disableDrag')
                document.documentElement.removeEventListener('mousemove', drag)
                document.documentElement.removeEventListener('mouseup', stopDrag)
            }

            document.documentElement.addEventListener('mousemove', drag)
            document.documentElement.addEventListener('mouseup', stopDrag)
        }
    },
    getters: {
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
        startWidth: (state, computed) => Math.round(computed.marginedInnerWidth / 2),
        startWidthPx: (state, computed) => `${computed.startWidth}px`,
        topHeight: (state, computed) => Math.round(computed.marginedInnerHeight - computed.bottomHeight),
        topHeightPx: (state, computed) => `${computed.topHeight}px`,
        widthPx: state => `${state.width}px`
    },
    mutations: {
        disableDrag(state) {
            state.drag = false
        },
        enableDrag(state) {
            state.drag = true
        },
        ratio(state, ratio) {
            if (ratio >= 0.1 && ratio <= 0.1)
                state.ratio = ratio
        },
        resize(state, el) {
            const rect = el.getBoundingClientRect()
            state.top = rect.top
            if (window.top !== null) {
                state.height = window.top.innerHeight - rect.top - 5
                state.width = window.top.innerWidth - 25
                state.windowWidth = window.top.innerWidth
                if (state.windowWidth < LARGE_SCREEN)
                    state.ratio = 0.5
            }
        }
    },
    namespaced: true,
    state: {
        drag: false,
        height: 0,
        marginEnd: 10,
        marginTop: 10,
        padding: 10,
        ratio: 0.5,
        top: 0,
        width: 0,
        windowWidth: 0
    }
}
