import api from '../api'
import {defineStore} from 'pinia'

export default defineStore('user', {
    actions: {
        async connect(data) {
            await api('/api/login', 'POST', data)
        },
        // eslint-disable-next-line no-empty-function
        async fetch() {
        }
    },
    getters: {
        isLogged: state => state.id > 0
    }
})
