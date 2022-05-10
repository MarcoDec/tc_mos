import {defineStore} from 'pinia'

export default defineStore('user', {
    actions: {
        connect(user) {
            this.id = user.id
            this.name = user.name
            this.roles = user.roles
        }
    },
    state: () => ({id: 0, name: null, roles: []})
})
