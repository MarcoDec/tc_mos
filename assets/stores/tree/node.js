import {get, set} from 'lodash'
import api from '../../api'
import {defineStore} from 'pinia'

export default function useNode(node, tree) {
    let initialState = {...node}
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
            initUpdate(fields) {
                this.updated = {}
                for (const field of fields.fields)
                    if (field.type !== 'file')
                        set(this.updated, field.name, get(this, field.name))
            },
            open() {
                this.opened = true
                this.parentStore?.open()
            },
            async remove() {
                await api(this.url, 'DELETE')
                this.dispose()
            },
            async update(data) {
                initialState = await api(this.url, 'POST', data)
                this.$reset()
                this.focus()
            }
        },
        getters: {
            bg: state => `bg-${state.selected ? 'warning' : 'none'}`,
            children: state => state.tree.findByParent(state['@id']),
            hasChildren() {
                return this.children.length > 0
            },
            icon: state => `chevron-${state.opened ? 'up' : 'down'}`,
            option(state) {
                return {'@id': this['@id'], text: state.fullName}
            },
            parentStore: state => state.tree.find(state.parent),
            root: state => state.parent === null,
            url: state => `${state.tree.url}/${state.id}`
        },
        state: () => ({...initialState, opened: false, selected: false, tree, updated: {}})
    })()
}
