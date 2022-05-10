import * as Cookies from '../../../cookie'
import {defineStore} from 'pinia'

export default defineStore('user', {
    actions: {
        connect(user) {
            this.id = user.id
            this.name = user.name
            this.roles = user.roles
            Cookies.set(user.id, user.token)
        },
        async fetchUser() {
            if (Cookies.has()) {
                try {
                    const response = await fetch(`/api/employees/${Cookies.get('id')}`, {
                        headers: {
                            Accept: 'application/ld+json',
                            'Content-Type': 'application/json'
                        },
                        method: 'GET'
                    })
                    if (response.status === 200) {
                        const user = await response.json()
                        this.connect(user)
                        return
                    }
                    // eslint-disable-next-line no-empty
                } catch (e) {
                }
            }
            Cookies.remove()
        }
    },
    state: () => ({id: 0, name: null, roles: []})
})
