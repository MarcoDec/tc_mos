import {computed, ref} from 'vue'
import api from '../api'
import {defineStore} from 'pinia'
import {useCookies} from '@vueuse/integrations/useCookies'

export default defineStore('user', () => {
    const cookies = useCookies(['id', 'token'])
    const id = ref(cookies.get('id') ?? 0)
    const isLogged = computed(() => id.value > 0)

    function save(response) {
        id.value = response.content.id
        cookies.set('id', response.content.id)
        cookies.set('token', response.content.token)
    }

    return {
        async connect(data) {
            const response = await api('/api/login', 'POST', data)
            if (response.status === 200)
                save(response)
        },
        cookies,
        async fetch() {
            const response = await api(`/api/employees/${id.value}`)
            if (response.status === 200)
                save(response)
            else {
                // eslint-disable-next-line require-atomic-updates
                id.value = 0
                cookies.remove('id')
                cookies.remove('token')
            }
        },
        id,
        isLogged,
        save
    }
})
