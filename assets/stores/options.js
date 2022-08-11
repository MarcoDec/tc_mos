import Api from '../Api'
import {defineStore} from 'pinia'

export default function generateOptions(type, valueProp = '@id') {
    return defineStore(`options/${type}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            },
            async fetch() {
                this.$reset()
                const response = await new Api().fetch(this.url)
                this.items = response.content['hydra:member']
            }
        },
        getters: {
            label: state => value => state.items.find(item => item[valueProp] === value)?.text ?? null,
            options: state => state.items.map(item => ({text: item.text, value: item[valueProp]})),
            url: state => `/api/${state.type}/options`
        },
        state: () => ({items: [], type})
    })()
}
