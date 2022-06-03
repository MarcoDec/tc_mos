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
        isHrAdmin() {
            return this.has('ROLE_HR_ADMIN')
        },
        isHrReader() {
            return this.isHrWriter || this.has('ROLE_HR_READER')
        },
        isHrWriter() {
            return this.isHrAdmin || this.has('ROLE_HR_WRITER')
        },
        isItAdmin() {
            return this.has('ROLE_IT_ADMIN')
        },
        isLogged: state => state.id > 0,
        isLogisticsAdmin() {
            return this.has('ROLE_LOGISTICS_ADMIN')
        },
        isLogisticsReader() {
            return this.isLogisticsWriter || this.has('ROLE_LOGISTICS_READER')
        },
        isLogisticsWriter() {
            return this.isLogisticsAdmin || this.has('ROLE_LOGISTICS_WRITER')
        },
        isManagementAdmin() {
            return this.has('ROLE_MANAGEMENT_ADMIN')
        },
        isManagementReader() {
            return this.isManagementWriter || this.has('ROLE_MANAGEMENT_READER')
        },
        isManagementWriter() {
            return this.isManagementAdmin || this.has('ROLE_MANAGEMENT_WRITER')
        },
        isProductionAdmin() {
            return this.has('ROLE_PRODUCTION_ADMIN')
        },
        isProductionReader() {
            return this.isProductionWriter || this.has('ROLE_PRODUCTION_READER')
        },
        isProductionWriter() {
            return this.isProductionAdmin || this.has('ROLE_PRODUCTION_WRITER')
        },
        isProjectAdmin() {
            return this.has('ROLE_PROJECT_ADMIN')
        },
        isProjectReader() {
            return this.isProjectWriter || this.has('ROLE_PROJECT_READER')
        },
        isProjectWriter() {
            return this.isProjectAdmin || this.has('ROLE_PROJECT_WRITER')
        },
        isPurchaseAdmin() {
            return this.has('ROLE_PURCHASE_ADMIN')
        },
        isPurchaseReader() {
            return this.isPurchaseWriter || this.has('ROLE_PURCHASE_READER')
        },
        isPurchaseWriter() {
            return this.isPurchaseAdmin || this.has('ROLE_PURCHASE_WRITER')
        },
        isQualityAdmin() {
            return this.has('ROLE_QUALITY_ADMIN')
        },
        isQualityReader() {
            return this.isQualityWriter || this.has('ROLE_QUALITY_READER')
        },
        isQualityWriter() {
            return this.isQualityAdmin || this.has('ROLE_QUALITY_WRITER')
        }
    },
    state: () => ({id: 0, name: null, roles: []})
})
