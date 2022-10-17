import {defineStore} from 'pinia'
import useField from './field'

export default function useFields(id, initialFields) {
    const store = defineStore(`fields/${id}`, {
        actions: {
            push(fields) {
                for (const field of fields)
                    this.fields.push(useField(field, this))
            }
        },
        getters: {
            action() {
                return this.create || this.search || this.update
            },
            create: state => state.fields.some(field => field.create),
            search: state => state.fields.some(field => field.search),
            update: state => state.fields.some(field => field.update)
        },
        state: () => ({fields: [], id})
    })()
    store.push(initialFields)
    return store
}
