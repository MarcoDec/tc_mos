import {defineStore} from 'pinia'
import fetchApi from '../../Api'

export const actionsItem = {
    dispose() {
        this.$reset()
        this.$dispose()
    },
    async update(data) {
        const response = await fetchApi(this.iri, 'PATCH', data)
        if (response.status === 422) throw response.content.violations
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
