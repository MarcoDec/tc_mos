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
    let content = null
    switch (response.status) {
    case 200:
        content = await response.json()
        return {content, status: response.status}
    case 204:
        return {status: response.status}
    case 401:
        content = await response.json()
        throw content
    default:
        throw response.statusText
    }
}
