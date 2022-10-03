import {computed, ref} from 'vue'
import api from '../api'
import {defineStore} from 'pinia'
import {useCookies} from '@vueuse/integrations/useCookies'

export default defineStore('user', () => {
    const cookies = useCookies(['id', 'token'])
    const id = ref(cookies.get('id') ?? 0)
    const isLogged = computed(() => id.value > 0)

    function clear() {
        id.value = 0
        cookies.remove('id')
        cookies.remove('token')
    }

    function save(response) {
        id.value = response.content.id
        cookies.set('id', response.content.id)
        cookies.set('token', response.content.token)
    }

    return {
        clear,
        async connect(data) {
            try {
                const response = await api('/api/login', 'POST', data)
                save(response)
            } catch (error) {
                clear()
                throw error
            }
        },
        cookies,
        async fetch() {
            if (isLogged.value) {
                try {
                    const response = await api(`/api/employees/${id.value}`)
                    save(response)
                    return
                    // eslint-disable-next-line no-empty
                } catch {
                }
            }
            clear()
        },
        id,
        isLogged,
        save
    }
})
