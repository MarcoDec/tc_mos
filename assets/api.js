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
        init.headers['Content-Type'] = 'application/json'
        if (!['delete', 'get'].includes(method))
            init.body = JSON.stringify(body)
        for (const key in body)
            if (generatedUrl.includes(`{${key}}`))
                generatedUrl = generatedUrl.replace(`{${key}}`, body[key])
    }
    Cookies.renew()
    const response = await fetch(generatedUrl, init)
    if (method === 'delete')
        return null
    if ([401, 422].includes(response.status))
        throw response
    if (response.status !== 204) {
        const json = await response.json()
        return json
    }
}
