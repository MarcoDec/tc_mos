import {defineStore} from 'pinia'

export default function generateFamily(family, root) {
    const name = `component-family/${family.id}`
    return defineStore(name, {
        actions: {
            blur() {
                this.opened = false
                this.selected = false
            },
            dispose() {
                this.$reset()
                this.$dispose()
            },
            focus() {
                this.root.blur()
                this.selected = true
                this.open()
            },
            open() {
                this.opened = true
                this.parentStore?.open()
            }
        },
        getters: {
            children: state => state.root.findByParent(state['@id']),
            fullName(state) {
                return this.parentStore ? `${this.parentStore.fullName}\\${state.name}` : state.name
            },
            hasChildren() {
                return this.children.length > 0
            },
            isRoot: state => !state.parent,
            parentStore: state => state.root.find(state.parent)
        },
        state: () => ({opened: false, root, selected: false, ...family})
    })()
}
