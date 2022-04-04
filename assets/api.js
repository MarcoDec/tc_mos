import * as Cookies from './cookie'

// eslint-disable-next-line consistent-return
export default async function fetchApi(url, method, body) {
    const headers = {Accept: 'application/ld+json'}
    const token = Cookies.get('token')
    if (typeof token === 'string')
        headers.Authorization = `Bearer ${token}`
    const init = {headers, method}
    let generatedUrl = url
    if (body instanceof FormData) {
        init.body = body
        body.forEach((value, key) => {
            if (generatedUrl.includes(`{${key}}`))
                generatedUrl = generatedUrl.replace(`{${key}}`, value)
        })
    } else {
        for (const key in body)
            if (body[key] === null)
                delete body[key]
        init.headers['Content-Type'] = 'application/json'
        if (!['delete', 'get'].includes(method))
            init.body = JSON.stringify(body)
        for (const key in body)
            if (generatedUrl.includes(`{${key}}`))
                generatedUrl = generatedUrl.replace(`{${key}}`, body[key])
            else if (method === 'get')
                generatedUrl += `${generatedUrl.includes('?') ? '&' : '?'}${key}=${body[key]}`
    }
    Cookies.renew()
    const response = await fetch(generatedUrl, init)
    if (method === 'delete')
        return null
    if (response.status >= 400 && response.status < 600)
        throw response
    if (response.status !== 204) {
        const json = await response.json()
        return json
    }
}
