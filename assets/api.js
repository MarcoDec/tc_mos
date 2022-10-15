import {useCookies} from '@vueuse/integrations/useCookies'

export default async function api(url, method = 'GET', body = null) {
    const init = {
        headers: {
            Accept: 'application/ld+json',
            'Content-Type': method === 'PATCH' ? 'application/merge-patch+json' : 'application/json'
        },
        method
    }
    const urlBuilder = new URL(url, location.origin)
    if (body !== null) {
        if (method === 'GET') {
            for (const [key, value] of Object.entries(body))
                urlBuilder.searchParams.append(key, value)
        } else
            init.body = JSON.stringify(body)
    }
    const token = useCookies(['token']).get('token')
    if (token)
        init.headers.Authorization = `Bearer ${token}`
    const response = await fetch(urlBuilder.href, init)
    switch (response.status) {
    case 200:
    case 201:
        return response.json()
    case 204:
        return response.status
    case 401:
        throw await response.json()
    case 422: {
        const content = await response.json()
        throw content.violations
    }
    default:
        throw response.statusText
    }
}
