import api from '../../api'
import {defineStore} from 'pinia'
import useOption from './option'

export default function useOptions(base, valueProp = '@id') {
    const id = `options/${base}`
    return defineStore(id, {
        actions: {
            dispose() {
                for (const option of this.options)
                    option.dispose()
                this.$reset()
                this.$dispose()
            },
            async fetch() {
                const response = await api(this.url)
                for (const option of response['hydra:member'])
                    this.options.push(useOption(option, this))
            },
            resetItems() {
                const options = [...this.options]
                this.options = []
                for (const option of options)
                    option.dispose()
            }
        },
        getters: {
            groups: state => {
                if (!state.options.every(option => Boolean(option.group)))
                    return []
                const groups = {}
                for (const option of state.options) {
                    if (typeof groups[option.group] === 'undefined')
                        groups[option.group] = []
                    groups[option.group].push(option)
                }
                return Object.entries(groups).map(([group, options]) => ({label: group, options})).reverse()
            },
            hasGroups() {
                return this.groups.length > 0
            },
            label: state => value => state.options.find(option => option.value === value)?.text ?? null,
            url: state => `/api/${state.base}/options`
        },
        state: () => ({base, id, options: [], valueProp})
    })()
}

export function prepareOptions(id, valueProp = '@id') {
    return {
        generate() {
            return useOptions(this.id, this.valueProp)
        },
        id,
        valueProp
    }
}
