import * as Cookies from './cookie'
import {set} from './utils'

export default class Api {
    constructor(fields = []) {
        this.fields = {}
        for (const field of fields)
            this.fields[field.name] = field.type
    }

    convertValue(key, value) {
        switch (this.fields[key]) {
        case 'boolean':
            return value === 'true'
        case 'number':
            return parseFloat(value)
        default:
            return typeof value === 'string' && value.startsWith('[') && value.endsWith(']') ? JSON.parse(value) : value
        }
    }

    stringify(data) {
        const entries = Object.fromEntries(data)
        const obj = {}
        for (const [key, value] of Object.entries(entries))
            set(obj, key, this.convertValue(key, value))
        return JSON.stringify(obj)
    }

    async fetch(url, method = 'GET', body = null, json = true) {
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
            init.body = json ? this.stringify(body) : body
        else if (method === 'GET' && typeof body === 'object' && body !== null)
            for (const [key, value] of Object.entries(body))
                normalizedUrl += `${normalizedUrl.includes('?') ? '&' : '?'}${key}=${value}`
        const response = await fetch(normalizedUrl, init)
        const content = response.status === 204 ? null : await response.json()
        return {content, status: response.status}
    }
}
