import {defineStore} from 'pinia'
const x = 0
export const useOverviewStore = defineStore('overview', {
    actions: {
    },
    getters: {
        count: state => state.items.reduce((sum, current) => current.value + sum, x)
    },
    state: () => ({
        items: [{name: 'enregistré', value: 10}, {name: 'aaaa', value: 10}, {name: 'bbb', value: 10}]
    })
})
