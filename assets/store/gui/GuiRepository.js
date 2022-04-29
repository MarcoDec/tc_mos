import {Gui, Repository} from '../modules'

export default class GuiRepository extends Repository {
    use = Gui

    drag(id) {
        const drag = ({y}) => {
            const gui = this.find(id)
            this.ratio(id, (gui.innerHeight - y + gui.marginTop) / gui.innerHeight)
        }

        const stopDrag = () => {
            this.disableDrag(id)
            document.documentElement.removeEventListener('mousemove', drag)
            document.documentElement.removeEventListener('mouseup', stopDrag)
        }

        document.documentElement.addEventListener('mousemove', drag)
        document.documentElement.addEventListener('mouseup', stopDrag)
    }

    disableDrag(id) {
        this.save({drag: false, id})
    }

    enableDrag(id) {
        this.save({drag: true, id})
    }

    ratio(id, ratio) {
        if (ratio >= 0.1 && ratio <= 0.9)
            this.save({id, ratio})
    }

    resize(id, el) {
        const rect = el.getBoundingClientRect()
        const gui = {id, top: rect.top}
        if (window.top !== null) {
            gui.height = window.top.innerHeight - rect.top - 5
            if (gui.height < 500)
                gui.height = 500
            gui.width = window.top.innerWidth - 25
            if (gui.width < 250)
                gui.width = 250
            gui.windowWidth = window.top.innerWidth
            if (gui.windowWidth < 1140)
                gui.ratio = 0.5
        }
        this.save(gui)
    }
}
