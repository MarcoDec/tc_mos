import {defineStore} from 'pinia'
import fetchApi from '../../../../api'

export default function generateFamily(family, root) {
    return defineStore(`component-family/${family.id}`, {
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
            },
            async remove() {
                await fetchApi(`/api/component-families/${this.id}`, 'DELETE')
                this.root.remove(this['@id'])
                this.dispose()
            },
            async update(data) {
                const response = await fetchApi(`/api/component-families/${this.id}`, 'POST', data, false)
                if (response.status === 422)
                    throw response.content.violations
                this.$state = {opened: this.opened, root: this.root, selected: this.selected, ...response.content}
            }
        },
        getters: {
            children: state => state.root.findByParent(state['@id']).sort((a, b) => a.name.localeCompare(b.name)),
            form: state => fields => {
                const form = {}
                for (const field of fields)
                    form[field.name] = state[field.name]
                form.file = state.filepath ?? '/img/no-image.png'
                return form
            },
            fullName(state) {
                return this.parentStore ? `${this.parentStore.fullName}\\${state.name}` : state.name
            },
            hasChildren() {
                return this.children.length > 0
            },
            isRoot: state => !state.parent,
            option: state => ({text: state.fullName, value: state['@id']}),
            parentStore: state => state.root.find(state.parent)
        },
        state: () => ({opened: false, root, selected: false, ...family})
    })()
}
