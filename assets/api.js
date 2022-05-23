import * as Cookies from './cookie'

export default async function fetchApi(url, method = 'GET', body = null, json = true) {
    const init = {headers: {Accept: 'application/ld+json'}, method}
    let normalizedUrl = url
    if (method === 'PATCH')
        init.headers['Content-Type'] = 'application/merge-patch+json'
    else if (json)
        init.headers['Content-Type'] = 'application/json'
    const token = Cookies.get('token')
    if (token)
        init.headers.Authorization = `Bearer ${token}`
    if (body instanceof FormData)
        init.body = json ? JSON.stringify(Object.fromEntries(body)) : body
    else if (method === 'GET' && typeof body === 'object' && body !== null)
        for (const [key, value] of Object.entries(body))
            normalizedUrl += `${normalizedUrl.includes('?') ? '&' : '?'}${key}=${value}`
    const response = await fetch(normalizedUrl, init)
    const content = response.status === 204 ? null : await response.json()
    return {content, status: response.status}
}
