import * as Cookies from './cookie'

export default async function fetchApi(url, method = 'GET', body = null, json = true) {
    const init = {headers: {Accept: 'application/ld+json'}, method}
    if (json)
        init.headers['Content-Type'] = 'application/json'
    const token = Cookies.get('token')
    if (token)
        init.headers.Authorization = `Bearer ${token}`
    if (body instanceof FormData)
        init.body = json ? JSON.stringify(Object.fromEntries(body)) : body
    const response = await fetch(url, init)
    const content = response.status === 204 ? null : await response.json()
    return {content, status: response.status}
}
