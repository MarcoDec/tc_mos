import {defineStore} from 'pinia'

export default function useNode(node, tree) {
    return defineStore(`${tree.$id}/${node.id}`, {
        actions: {
            blur() {
                this.opened = false
                this.selected = false
            },
            dispose() {
                this.tree.removeNode(this)
                this.$dispose()
            },
            focus() {
                this.tree.blur()
                this.selected = true
                this.open()
            },
            open() {
                this.opened = true
                this.parentStore?.open()
            }
        },
        getters: {
            bg: state => `bg-${state.selected ? 'warning' : 'none'}`,
            children: state => state.tree.findByParent(state['@id']),
            hasChildren() {
                return this.children.length > 0
            },
            icon: state => `chevron-${state.opened ? 'up' : 'down'}`,
            parentStore: state => state.tree.find(state.parent),
            root: state => state.parent === null
        },
        state: () => ({...node, opened: false, selected: false, tree})
    })()
}
