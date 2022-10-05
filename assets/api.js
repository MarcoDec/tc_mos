import {useCookies} from '@vueuse/integrations/useCookies'

export default async function api(url, method = 'GET', body = null) {
    const init = {
        headers: {Accept: 'application/ld+json', 'Content-Type': 'application/json'},
        method
    }
    if (method !== 'GET' && body !== null)
        init.body = JSON.stringify(body)
    const token = useCookies(['token']).get('token')
    if (token)
        init.headers.Authorization = `Bearer ${token}`
    const response = await fetch(url, init)
    if (response.status === 200) {
        const content = await response.json()
        return {content, status: response.status}
    }
    if (response.status === 401) {
        const content = await response.json()
        throw content
    }
    throw response.statusText
}
