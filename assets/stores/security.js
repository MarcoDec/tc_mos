import {computed, ref} from 'vue'
import api from '../api'
import {defineStore} from 'pinia'
import {useCookies} from '@vueuse/integrations/useCookies'

function defineUserStore() {
    const store = defineStore('user', () => {
        const cookies = useCookies(['token'])
        const id = ref(0)
        const name = ref(null)
        const roles = ref([])

        const isHrAdmin = computed(() => roles.value.includes('ROLE_HR_ADMIN'))
        const isHrWriter = computed(() => isHrAdmin.value || roles.value.includes('ROLE_HR_WRITER'))
        const isLogisticsAdmin = computed(() => roles.value.includes('ROLE_LOGISTICS_ADMIN'))
        const isLogisticsWriter = computed(() => isLogisticsAdmin.value || roles.value.includes('ROLE_LOGISTICS_WRITER'))
        const isManagementAdmin = computed(() => roles.value.includes('ROLE_MANAGEMENT_ADMIN'))
        const isManagementWriter = computed(() => isManagementAdmin.value || roles.value.includes('ROLE_MANAGEMENT_WRITER'))
        const isProductionAdmin = computed(() => roles.value.includes('ROLE_PRODUCTION_ADMIN'))
        const isProductionWriter = computed(() => isProductionAdmin.value || roles.value.includes('ROLE_PRODUCTION_WRITER'))
        const isProjectAdmin = computed(() => roles.value.includes('ROLE_PROJECT_ADMIN'))
        const isProjectWriter = computed(() => isProjectAdmin.value || roles.value.includes('ROLE_PROJECT_WRITER'))
        const isPurchaseAdmin = computed(() => roles.value.includes('ROLE_PURCHASE_ADMIN'))
        const isPurchaseWriter = computed(() => isPurchaseAdmin.value || roles.value.includes('ROLE_PURCHASE_WRITER'))
        const isQualityAdmin = computed(() => roles.value.includes('ROLE_QUALITY_ADMIN'))
        const isQualityWriter = computed(() => isQualityAdmin.value || roles.value.includes('ROLE_QUALITY_WRITER'))

        function clear() {
            store.$reset()
            cookies.remove('token')
        }

        function save(response) {
            id.value = response.id
            name.value = response.name
            roles.value = response.roles
            cookies.set('token', response.token)
        }

        return {
            clear,
            async connect(data) {
                try {
                    save(await api('/api/login', 'POST', data))
                } catch (error) {
                    clear()
                    throw error
                }
            },
            cookies,
            async fetch() {
                if (cookies.get('token')) {
                    try {
                        save(await api('/api/user'))
                        return
                        // eslint-disable-next-line no-empty
                    } catch {
                    }
                }
                clear()
            },
            id,
            isHrAdmin,
            isHrReader: computed(() => isHrWriter.value || roles.value.includes('ROLE_HR_READER')),
            isHrWriter,
            isItAdmin: computed(() => roles.value.includes('ROLE_IT_ADMIN')),
            isLogged: computed(() => id.value > 0),
            isLogisticsAdmin,
            isLogisticsReader: computed(() => isLogisticsWriter.value || roles.value.includes('ROLE_LOGISTICS_READER')),
            isLogisticsWriter,
            isManagementAdmin,
            isManagementReader: computed(() => isManagementWriter.value || roles.value.includes('ROLE_MANAGEMENT_READER')),
            isManagementWriter,
            isProductionAdmin,
            isProductionReader: computed(() => isProductionWriter.value || roles.value.includes('ROLE_PRODUCTION_READER')),
            isProductionWriter,
            isProjectAdmin,
            isProjectReader: computed(() => isProjectWriter.value || roles.value.includes('ROLE_PROJECT_READER')),
            isProjectWriter,
            isPurchaseAdmin,
            isPurchaseReader: computed(() => isPurchaseWriter.value || roles.value.includes('ROLE_PURCHASE_READER')),
            isPurchaseWriter,
            isQualityAdmin,
            isQualityReader: computed(() => isQualityWriter.value || roles.value.includes('ROLE_QUALITY_READER')),
            isQualityWriter,
            async logout() {
                try {
                    await api('/api/logout', 'POST')
                    // eslint-disable-next-line no-empty
                } catch {
                }
                clear()
            },
            name,
            roles,
            save,
            setup: true
        }
    })()
    return store
}

let userStore = null

export default function useUser() {
    if (userStore === null)
        userStore = defineUserStore()
    return userStore
}