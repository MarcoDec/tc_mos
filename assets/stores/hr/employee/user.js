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
        async fetch() {
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
        has: state => role => state.roles.includes(role),
        isLogged: state => state.id > 0,
        isManagementAdmin() {
            return this.has('ROLE_MANAGEMENT_ADMIN')
        },
        isManagementReader() {
            return this.isManagementWriter || this.has('ROLE_MANAGEMENT_READER')
        },
        isManagementWriter() {
            return this.isManagementAdmin || this.has('ROLE_MANAGEMENT_WRITER')
        },
        isPurchaseAdmin() {
            return this.has('ROLE_PURCHASE_ADMIN')
        },
        isPurchaseReader() {
            return this.isPurchaseWriter || this.has('ROLE_PURCHASE_READER')
        },
        isPurchaseWriter() {
            return this.isPurchaseAdmin || this.has('ROLE_PURCHASE_WRITER')
        }
    },
    state: () => ({id: 0, name: null, roles: []})
})
