import Api from '../../api'
import {defineStore} from 'pinia'

export default function generateItem(iriType, item, root) {
    return defineStore(`${iriType}/${item.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            },
            async remove() {
                await new Api().fetch(this.iri, 'DELETE')
                this.root.remove(this['@id'])
                this.dispose()
            },
            async update(fields, data) {
                const response = await new Api(fields).fetch(this.iri, 'PATCH', data)
                if (response.status === 422)
                    throw response.content.violations
                this.$state = {iriType: this.iriType, root: this.root, ...response.content}
            }
        },
        getters: {
            iri: state => `/api/${state.iriType}/${state.id}`
        },
        state: () => ({iriType, root, ...item})
    })()
}
