import api from '../../api'
import {defineStore} from 'pinia'
import useOption from './option'

export default function useOptions(base) {
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
                for (const option of response.content['hydra:member'])
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
            label: state => value => state.options.find(option => option.value === value)?.text ?? null,
            url: state => `/api/${state.base}/options`
        },
        state: () => ({base, id, options: []})
    })()
}

export function prepareOptions(id) {
    return {
        generate() {
            return useOptions(this.id)
        },
        id
    }
}
