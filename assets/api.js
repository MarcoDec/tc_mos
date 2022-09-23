import {useCookies} from '@vueuse/integrations/useCookies'

export default async function api(url, method = 'GET', body = null) {
    const init = {
        headers: {Accept: 'application/ld+json', 'Content-Type': 'application/json'},
        method
    }
    if (method !== 'GET')
        init.body = JSON.stringify(body)
    const token = useCookies(['token']).get('token')
    if (token)
        init.headers.Authorization = `Bearer ${token}`
    const response = await fetch(url, init)
    const content = await response.json()
    return {content, status: response.status}
}
