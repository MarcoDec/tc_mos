import api from '../../api'
import {defineStore} from 'pinia'

export default function generateItem(iriType, item, root) {
    return defineStore(`${iriType}/${item.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            },
            async remove() {
                await api().fetch(this.iri, 'DELETE')
                this.root.remove(this['@id'])
                this.dispose()
            },
            async update(fields, data) {
                const response = await api(fields).fetch(this.iri, 'PATCH', data)
                this.$state = {iriType: this.iriType, root: this.root, ...response}
            }
        },
        getters: {
            iri: state => `/api/${state.iriType}/${state.id}`
        },
        state: () => ({iriType, root, ...item})
    })()
}
