import Api from '../../Api'
import {defineStore} from 'pinia'

export default function generateFamily(iriType, family, root) {
    return defineStore(`${iriType}/${family.id}`, {
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
                await new Api().fetch(this.iri, 'DELETE')
                this.root.remove(this['@id'])
                this.dispose()
            },
            async update(fields, data) {
                const response = await new Api(fields).fetch(this.iri, 'POST', data, false)
                if (response.status === 422)
                    throw response.content.violations
                this.$state = {opened: this.opened, root: this.root, selected: this.selected, ...response.content}
            },
            async updateAttributes(data) {
                const response = await new Api().fetch(this.iri, 'PATCH', data)
                return response.content.attributes
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
            iri: state => `/api/${state.iriType}/${state.id}`,
            isRoot: state => !state.parent,
            option: state => ({text: state.fullName, value: state['@id']}),
            parentStore: state => state.root.find(state.parent)
        },
        state: () => ({iriType, opened: false, root, selected: false, ...family})
    })()
}
