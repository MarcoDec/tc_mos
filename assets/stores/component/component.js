import api from '../../api'
import {defineStore} from 'pinia'

export const useComponentListStore = defineStore('component', {
    actions: {
        async fetch() {
            const response = await api('/api/components/1', 'GET')
            console.log('response===', response)
            this.component = await response['hydra:member']
        }
        // async update(data) {
        //     console.log('je suis ici')
        //     const response = await api('/api/component-attributes/1', 'PATCH', data)
        //     console.log('response update', response)
        //     // /this.componentAttribute = await response['hydra:member']
        // }

    },
    getters: {

    },
    state: () => ({
        component: []
    })
})
