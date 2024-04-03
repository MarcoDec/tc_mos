import {computed, ref} from 'vue'
import api from '../api'
import {defineStore} from 'pinia'
import {useCookies} from '@vueuse/integrations/useCookies'
//import CryptoJS from 'crypto-js'

function defineUserStore() {
    const store = defineStore('user', () => {
        const cookies = useCookies(['token'])
        const id = ref(0)
        const name = ref(null)
        const roles = ref([])
        const company = ref(null)
        const isHrAdmin = computed(() => roles.value.includes('ROLE_HR_ADMIN'))
        const isHrWriter = computed(() => isHrAdmin.value || roles.value.includes('ROLE_HR_WRITER'))
        const isLogisticsAdmin = computed(() => roles.value.includes('ROLE_LOGISTICS_ADMIN'))
        const isLogisticsWriter = computed(() => isLogisticsAdmin.value || roles.value.includes('ROLE_LOGISTICS_WRITER'))
        const isMaintenanceAdmin = computed(() => roles.value.includes('ROLE_MAINTENANCE_ADMIN'))
        const isMaintenanceWriter = computed(() => isMaintenanceAdmin.value || roles.value.includes('ROLE_MAINTENANCE_WRITER'))
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
        const isSellingAdmin = computed(() => roles.value.includes('ROLE_SELLING_ADMIN'))
        const isSellingWriter = computed(() => isSellingAdmin.value || roles.value.includes('ROLE_SELLING_WRITER'))

        function clear() {
            store.$reset()
            cookies.remove('token')
        }

        function save(response) {
            id.value = response.id
            name.value = response.name
            roles.value = response.roles
            company.value = response.company
            cookies.set('token', response.token)
        }

        return {
            clear,
            company,
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
                // Récupération du token figurant dans l'url
                const url = new URL(window.location.href)
                const token = url.searchParams.get('token')
                if (token) {
                    console.log('token trouvé dans l\'url', token)
                    url.searchParams.delete('token')
                    window.history.replaceState({}, '', url)
                    cookies.set('token', token)
                    console.log('ajout token dans cookie')
                } else {
                    console.log('token pas trouvé dans l\'url')
                }
                if (cookies.get('token')) {
                    console.log('token trouvé dans les cookies')
                    try {
                        save(await api('/api/user'))
                        console.log('Utilisateur authentifié')
                        return
                    } catch {
                        console.log('erreur d\'authentification')
                    }
                } else {
                    console.log('Token pas trouvé dans les cookies')
                }
                clear()
            },
            id,
            isHrAdmin,
            isHrReader: computed(() => isHrWriter.value || roles.value.includes('ROLE_HR_READER')),
            isHrWriter,
            isItAdmin: computed(() => roles.value.includes('ROLE_IT_ADMIN')),
            isItReader: computed(() => roles.value.includes('ROLE_IT_READER')),
            isItWriter: computed(() => roles.value.includes('ROLE_IT_WRITER')),
            isLogged: computed(() => id.value > 0),
            isLogisticsAdmin,
            isLogisticsReader: computed(() => isLogisticsWriter.value || roles.value.includes('ROLE_LOGISTICS_READER')),
            isLogisticsWriter,
            isMaintenanceAdmin,
            isMaintenanceReader: computed(() => isMaintenanceWriter.value || roles.value.includes('ROLE_MAINTENANCE_READER')),
            isMaintenanceWriter,
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
            isSellingAdmin,
            isSellingReader: computed(() => isSellingWriter.value || roles.value.includes('ROLE_SELLING_READER')),
            isSellingWriter,
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
