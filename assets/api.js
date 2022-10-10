import {useCookies} from '@vueuse/integrations/useCookies'

export default async function api(url, method = 'GET', body = null) {
    const init = {
        headers: {Accept: 'application/ld+json', 'Content-Type': 'application/json'},
        method
    }
    let normalizedUrl = url
    if (body !== null) {
        if (method === 'GET')
            for (const [key, value] of Object.entries(body))
                normalizedUrl += `${normalizedUrl.includes('?') ? '&' : '?'}${key}=${value}`
        else
            init.body = JSON.stringify(body)
    }
    const token = useCookies(['token']).get('token')
    if (token)
        init.headers.Authorization = `Bearer ${token}`
    const response = await fetch(normalizedUrl, init)
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
