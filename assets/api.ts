/* eslint-disable @typescript-eslint/ban-types,@typescript-eslint/ban-ts-comment,consistent-return */
import * as Cookies from './cookie'
import type {Merge} from './types/types'
import type {paths} from './types/openapi'

declare type Content<T> = T extends {requestBody: {content: infer A}} ? A : {}
declare type Responses<T> = T extends {responses: infer A} ? A : {}

export declare type Urls = keyof paths
declare type Operations<U extends Urls> = paths[U]
export declare type Methods<U extends Urls> = keyof Operations<U>
declare type Operation<U extends Urls, M extends Methods<U>> = Operations<U>[M]
declare type Parameters<U extends Urls, M extends Methods<U>> = Operation<U, M> extends {parameters: {path: infer A}} ? A : {}
declare type BodyContent<U extends Urls, M extends Methods<U>> =
    Content<Operation<U, M>> extends {'multipart/form-data': Record<string, unknown>}
        ? FormData
        : Content<Operation<U, M>> extends {'application/json': infer A}
            ? A
            : {}
export declare type ApiBody<U extends Urls, M extends Methods<U>> = Merge<Parameters<U, M>, BodyContent<U, M>>
export declare type Response<U extends Urls, M extends Methods<U>> =
    Responses<Operation<U, M>> extends {201: {content: {'application/ld+json': infer A}}}
        ? A
        : Responses<Operation<U, M>> extends {200: {content: {'application/ld+json': infer A}}}
            ? A
            : never

declare type ApiHeaders = HeadersInit & {'Content-Type'?: string}

export default async function fetchApi<U extends Urls, M extends Methods<U>>(
    url: U,
    method: M,
    body: ApiBody<U, M>
    // @ts-ignore
): Promise<Response<U, M>> {
    const headers: ApiHeaders = {Accept: 'application/ld+json'}
    const token = Cookies.get('token')
    if (typeof token === 'string')
        headers.Authorization = `Bearer ${token}`
    const init: Omit<RequestInit, 'headers'> & {headers: ApiHeaders} = {headers, method: method as string}
    let generatedUrl: string = url
    if (body instanceof FormData) {
        init.body = body
        body.forEach((value, key) => {
            if (generatedUrl.includes(`{${key}}`))
                generatedUrl = generatedUrl.replace(`{${key}}`, value as string)
        })
    } else {
        init.headers['Content-Type'] = 'application/json'
        if (!['delete', 'get'].includes(method as string))
            init.body = JSON.stringify(body)
        for (const key in body)
            if (generatedUrl.includes(`{${key}}`))
                generatedUrl = generatedUrl.replace(`{${key}}`, body[key] as string)
    }
    Cookies.renew()
    const response = await fetch(generatedUrl, init)
    if (method === 'delete')
        return null as Response<U, M>
    if ([401, 422].includes(response.status))
        throw response
    if (response.status !== 204) {
        const json = await response.json() as Response<U, M>
        return json
    }
}
