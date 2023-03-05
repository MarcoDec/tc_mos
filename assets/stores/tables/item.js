import api from '../../api'
import {defineStore} from 'pinia'

export const actionsItem = {
    dispose() {
        this.$reset()
        this.$dispose()
    },
    async update(data) {
        const response = await api(this.iri, 'PATCH', data)
        this.$state = {
            iriType: this.iriType,
            root: this.root,
            ...response.content
        }
    }
}

export const gettersItem = {
    iri: state => `/api/${state.iriType}/${state.id}`
}

export default function generateItem(iriType, item, root) {
    return defineStore(`${iriType}/${item.id}`, {
        actions: {...actionsItem},
        getters: {...gettersItem},
        state: () => ({iriType, root, ...item})
    })()
}
