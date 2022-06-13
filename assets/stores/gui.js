import {defineAsyncComponent} from 'vue'
import {defineStore} from 'pinia'

export default defineStore('gui', {
    actions: {
        disableDrag() {
            this.drag = false
        },
        drag() {
            const drag = ({y}) => {
                this.setRatio((this.innerHeight - y + this.marginTop) / this.innerHeight)
            }

            const stopDrag = () => {
                this.disableDrag()
                document.documentElement.removeEventListener('mousemove', drag)
                document.documentElement.removeEventListener('mouseup', stopDrag)
            }

            document.documentElement.addEventListener('mousemove', drag)
            document.documentElement.addEventListener('mouseup', stopDrag)
        },
        enableDrag() {
            this.drag = true
        },
        resize(el) {
            const rect = el.getBoundingClientRect()
            this.top = rect.top
            if (window.top !== null) {
                this.height = window.top.innerHeight - rect.top - 5
                this.width = window.top.innerWidth - 25
                this.windowWidth = window.top.innerWidth
                if (this.windowWidth < 1140)
                    this.setRatio(0.5)
            }
        },
        setRatio(ratio) {
            if (ratio >= 0.1 && ratio <= 0.1)
                this.ratio = ratio
        }
    },
    getters: {
        bottomHeight(state) {
            return Math.round(this.marginedInnerHeight * state.ratio)
        },
        bottomHeightPx() {
            return `${this.bottomHeight}px`
        },
        endWidth() {
            return Math.round(this.marginedInnerWidth - this.startWidth)
        },
        endWidthPx() {
            return `${this.endWidth}px`
        },
        guiBottom: state => (state.windowWidth >= 1140
            ? defineAsyncComponent(() => import('../components/gui/AppShowGuiResizableCard'))
            : 'AppShowGuiCard'),
        heightPx: state => `${state.height}px`,
        icon: state => state.windowWidth <= 800,
        innerBottomHeight(state) {
            return this.bottomHeight - 2 * state.padding
        },
        innerBottomHeightPx() {
            return `${this.innerBottomHeight}px`
        },
        innerHeight: state => Math.round(state.height - 2 * state.padding),
        innerStartHeight(state) {
            return this.topHeight - 2 * state.padding
        },
        innerStartHeightPx() {
            return `${this.innerStartHeight}px`
        },
        innerWidth: state => Math.round(state.width - 2 * state.padding),
        innerWidthPx() {
            return `${this.innerWidth}px`
        },
        marginEndPx: state => `${state.marginEnd}px`,
        marginTopPx: state => `${state.marginTop}px`,
        marginedInnerHeight(state) {
            return this.innerHeight - state.marginTop
        },
        marginedInnerWidth(state) {
            return this.innerWidth - state.marginEnd
        },
        paddingPx: state => `${state.padding}px`,
        startWidth() {
            return Math.round(this.marginedInnerWidth / 2)
        },
        startWidthPx() {
            return `${this.startWidth}px`
        },
        topHeight() {
            return Math.round(this.marginedInnerHeight - this.bottomHeight)
        },
        topHeightPx() {
            return `${this.topHeight}px`
        },
        vertical: state => state.windowWidth <= 950,
        widthPx: state => `${state.width}px`
    },
    state: () => ({
        drag: false,
        height: 0,
        marginEnd: 10,
        marginTop: 10,
        padding: 10,
        ratio: 0.5,
        top: 0,
        width: 0,
        windowWidth: 0
    })
})
