import {defineStore} from 'pinia'
import fetchApi from '../../api'

export default function generateItem(iriType, item, root) {
    return defineStore(`${iriType}/${item.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            },
            async remove() {
                await fetchApi(this.iri, 'DELETE')
                this.root.remove(this['@id'])
                this.dispose()
            },
            async update(data) {
                const response = await fetchApi(this.iri, 'PATCH', data)
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
