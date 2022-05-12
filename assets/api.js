import * as Cookies from './cookie'

export default async function fetchApi(url, method = 'GET', body = null) {
    const init = {
        headers: {
            Accept: 'application/ld+json',
            'Content-Type': 'application/json'
        },
        method
    }
    const token = Cookies.get('token')
    if (token)
        init.headers.Authorization = `Bearer ${token}`
    if (body instanceof FormData)
        init.body = JSON.stringify(Object.fromEntries(body))
    const response = await fetch(url, init)
    const content = response.status === 204 ? null : await response.json()
    return {content, status: response.status}
}
