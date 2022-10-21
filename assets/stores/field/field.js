/* eslint-disable no-use-before-define */
import {defineStore} from 'pinia'
import useOptions from '../option/options'

export default function useField(field, parent) {
    const id = `${parent.$id}/${field.name}`
    const store = defineStore(id, {
        actions: {
            dispose() {
                if (this.measure !== null) {
                    this.measure.code.dispose()
                    this.measure.value.dispose()
                }
                if (this.options !== null)
                    this.options.dispose()
                this.$dispose()
            },
            async fetch() {
                if (this.measure !== null)
                    await this.measure.code.fetch()
                if (this.options !== null)
                    await this.options.fetch()
            }
        },
        getters: {
            findOption: state => value => state.options?.find(value) ?? null,
            labelValue: state => value => {
                if (state.type === 'measure') {
                    const code = state.measure.code.labelValue(value.code)
                    return code ? `${value.value} ${code}` : value.value
                }
                if (state.type === 'multiselect') {
                    const labels = []
                    for (const el of value)
                        labels.push(state.options.label(el))
                    return labels
                }
                if (state.options)
                    return state.options.label(value)
                return value
            }
        },
        state: () => {
            const state = {
                create: field.create ?? true,
                hideLabelValue: field.hideLabelValue ?? true,
                label: field.label,
                measure: null,
                name: field.name,
                options: null,
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
    if (store.type === 'measure')
        store.measure = useMeasure(store)
    else if (store.type === 'price') {
        store.measure = useMeasure(store, 'currencies')
        store.type = 'measure'
    }
    return store
}

function useMeasure(store, base = 'units') {
    return {
        code: useField(
            {label: 'Code', name: `${store.name}[code]`, options: {base, value: 'code'}, type: 'select'},
            store
        ),
        value: useField({label: 'Valeur', name: `${store.name}[value]`, type: 'number'}, store)
    }
}
