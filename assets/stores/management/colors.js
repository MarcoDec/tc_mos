import {defineStore} from 'pinia'
import fetchApi from '../../api'
import generateColor from './color'

export default defineStore('color', {
    actions: {
        dispose() {
            this.$reset()
            for (const color of this.items)
                color.dispose()
            this.$dispose()
        },
        async fetch() {
            const response = await fetchApi('/api/colors')
            if (response.status === 200)
                for (const color of response.content['hydra:member'])
                    this.items.push(generateColor(color, this))
        }
    },
    getters: {
        length: state => state.items.length
    },
    state: () => ({items: []})
})
