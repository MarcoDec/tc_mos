import {Model} from '../modules'

export default class Gui extends Model {
    static entity = 'guis'

    get bottomHeight() {
        return Math.round(this.marginedInnerHeight * this.ratio)
    }

    get bottomHeightPx() {
        return `${this.bottomHeight}px`
    }

    get endWidth() {
        return Math.round(this.marginedInnerWidth - this.startWidth)
    }

    get endWidthPx() {
        return `${this.endWidth}px`
    }

    get guiBottom() {
        return this.windowWidth >= 1140 ? 'AppShowGuiResizableCard' : 'AppShowGuiCard'
    }

    get heightPx() {
        return `${this.height}px`
    }

    get icon() {
        return this.windowWidth <= 800
    }

    get innerBottomHeight() {
        return this.bottomHeight - 2 * this.padding
    }

    get innerBottomHeightPx() {
        return `${this.innerBottomHeight}px`
    }

    get innerHeight() {
        return Math.round(this.height - 2 * this.padding)
    }

    get innerStartHeight() {
        return this.topHeight - 2 * this.padding
    }

    get innerStartHeightPx() {
        return `${this.innerStartHeight}px`
    }

    get innerWidth() {
        return Math.round(this.width - 2 * this.padding)
    }

    get innerWidthPx() {
        return `${this.innerWidth}px`
    }

    get marginEndPx() {
        return `${this.marginEnd}px`
    }

    get marginTopPx() {
        return `${this.marginTop}px`
    }

    get marginedInnerHeight() {
        return this.innerHeight - this.marginTop
    }

    get marginedInnerWidth() {
        return this.innerWidth - this.marginEnd
    }

    get paddingPx() {
        return `${this.padding}px`
    }

    get startWidth() {
        return Math.round(this.marginedInnerWidth / 2)
    }

    get startWidthPx() {
        return `${this.startWidth}px`
    }

    get topHeight() {
        return Math.round(this.marginedInnerHeight - this.bottomHeight)
    }

    get topHeightPx() {
        return `${this.topHeight}px`
    }

    get vertical() {
        return this.windowWidth <= 950
    }

    get widthPx() {
        return `${this.width}px`
    }

    static fields() {
        return {
            ...super.fields(),
            drag: this['boolean'](false),
            height: this.number(0),
            id: this.string(null),
            marginEnd: this.number(10),
            marginTop: this.number(10),
            padding: this.number(10),
            ratio: this.number(0.5),
            top: this.number(0),
            width: this.number(0),
            windowWidth: this.number(0)
        }
    }
}
