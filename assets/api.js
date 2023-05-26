/* eslint-disable accessor-pairs */
import {useCookies} from '@vueuse/integrations/useCookies'

class ApiRequest {
    #body = null
    #headers = new Headers()
    #method
    #url

    constructor(url, method) {
        this.#method = method
        this.#url = new URL(url, location.origin)

        this.#append('Accept', 'application/ld+json')
        const token = useCookies(['token']).get('token')
        if (token)
            this.#append('Authorization', `Bearer ${token}`)
    }

    set body(body) {
        if (body instanceof FormData)
            this.#formDataBody = body
        else {
            this.#append('Content-Type', this.#contentType)
            if (body !== null) {
                if (this.#method === 'GET')
                    for (const [key, value] of Object.entries(body))
                        this.#url.searchParams.append(key, value)
                else
                    this.#jsonBody = body
            }
        }
    }

    get request() {
        return new Request(this.#url.toString(), this.#init)
    }

    get #contentType() {
        return this.#method === 'PATCH' ? 'application/merge-patch+json' : 'application/json'
    }

    set #formDataBody(body) {
        for (const [key, value] of Object.entries(Object.fromEntries(body))) {
            if (typeof value === 'undefined' || value === null)
                body.delete(key)
            else if (typeof value === 'string') {
                body.set(key, value.trim())
                if (body.get(key).length === 0)
                    body.delete(key)
            }
        }
        this.#body = body
    }

    get #init() {
        return {body: this.#body, headers: this.#headers, method: this.#method}
    }

    set #jsonBody(body) {
        for (const [key, value] of Object.entries(body)) {
            if (typeof value === 'undefined' || value === null)
                delete body[key]
            else if (typeof value === 'string') {
                body[key] = value.trim()
                if (body[key].length === 0)
                    delete body[key]
            }
        }
        this.#body = JSON.stringify(body)
    }

    #append(name, value) {
        this.#headers.append(name, value)
    }
}

export default async function api(url, method = 'GET', body = null) {
    const request = new ApiRequest(url, method)
    request.body = body
    const response = await fetch(request.request)
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
