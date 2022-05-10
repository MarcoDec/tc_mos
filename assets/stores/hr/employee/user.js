import * as Cookies from '../../../cookie'
import {defineStore} from 'pinia'
import fetchApi from '../../../api'

export default defineStore('user', {
    actions: {
        async connect(data) {
            const response = await fetchApi('/api/login', 'POST', data)
            if (response.status === 200)
                this.save(response.content)
            else
                throw response.content
        },
        async fetchUser() {
            if (Cookies.has()) {
                try {
                    const response = await fetchApi(`/api/employees/${Cookies.get('id')}`)
                    if (response.status === 200) {
                        this.save(response.content)
                        return
                    }
                    // eslint-disable-next-line no-empty
                } catch (e) {
                }
            }
            Cookies.remove()
        },
        async logout() {
            await fetchApi('/api/logout', 'POST')
            this.$reset()
            Cookies.remove()
        },
        save(user) {
            this.id = user.id
            this.name = user.name
            this.roles = user.roles
            Cookies.set(user.id, user.token)
        }
    },
    getters: {
        isLogged: state => state.id > 0
    },
    state: () => ({id: 0, name: null, roles: []})
})
