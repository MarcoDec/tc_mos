import {computed, ref} from 'vue'
import api from '../api'
import {defineStore} from 'pinia'
import {useCookies} from '@vueuse/integrations/useCookies'

export default defineStore('user', () => {
    const cookies = useCookies(['token'])
    const id = ref(0)
    const name = ref(null)
    const isLogged = computed(() => id.value > 0)

    function clear() {
        id.value = 0
        cookies.remove('token')
    }

    function save(response) {
        id.value = response.content.id
        name.value = response.content.name
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
            if (cookies.get('token')) {
                try {
                    const response = await api('/api/user')
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
        name,
        save
    }
})
