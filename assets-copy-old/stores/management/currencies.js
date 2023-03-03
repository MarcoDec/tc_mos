import Api from '../../Api'
import {defineStore} from 'pinia'

function split(currencies) {
    const splitted = [[]]
    let i = 0
    for (const currency of currencies) {
        if (splitted[i].length >= 7)
            splitted[++i] = []
        splitted[i].push(currency)
    }
    return splitted
}

export default defineStore('currencies', {
    actions: {
        async fetch() {
            this.$reset()
            const response = await new Api().fetch('/api/currencies', 'GET')
            if (response.status === 200)
                this.items = response.content['hydra:member']
        },
        resetFilters() {
            this.active = false
            this.code = null
            this.name = null
        },
        async update(currency, active) {
            const data = new FormData()
            data.append('active', active)
            const response = await new Api([{name: 'active', type: 'boolean'}])
                .fetch(`/api/currencies/${currency.id}`, 'PATCH', data)
            if (response.status === 200)
                this.items[this.items.findIndex(item => item['@id'] === response.content['@id'])] = response.content
        }
    },
    getters: {
        split: state => split(state.items.filter(item => {
            if (state.active && !item.active)
                return false
            if (state.code && !item.code.toUpperCase().startsWith(state.code.toUpperCase()))
                return false
            if (state.name && !item.name.toUpperCase().startsWith(state.name.toUpperCase()))
                return false
            return true
        }))
    },
    state: () => ({active: false, code: null, items: [], name: null})
})
