import {defineStore} from 'pinia'

export default defineStore('user', {
    actions: {
        // eslint-disable-next-line no-empty-function
        async fetch() {}
    },
    getters: {
        isLogged: state => state.id > 0
    }
})
