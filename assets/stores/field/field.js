import {defineStore} from 'pinia'
import useOptions from '../option/options'

export default function useField(field, fields) {
    const id = `${fields.id}/${field.name}`
    return defineStore(id, {
        state: () => {
            const state = {
                create: field.create ?? true,
                hideLabelValue: field.hideLabelValue ?? true,
                label: field.label,
                name: field.name,
                options: field.options ? useOptions() : null,
                search: field.search ?? true,
                sort: field.sort ?? true,
                type: field.type ?? 'text',
                update: field.update ?? true
            }
            if (field.options) {
                if (Array.isArray(field.options)) {
                    state.options = useOptions(`${id}/${field.name}`, 'value')
                    state.options.options = field.options
                } else {
                    state.options = useOptions(field.options.base, field.options.value ?? '@id')
                    state.options.fetchable = true
                }
            }
            return state
        }
    })()
}
