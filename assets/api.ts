/* eslint-disable @typescript-eslint/ban-types,@typescript-eslint/no-magic-numbers */
import type {Merge} from './types/types'
import type {paths} from './types/openapi'

type Content<T> = T extends {requestBody: {content: infer A}} ? A : {}
type Responses<T> = T extends {responses: infer A} ? A : {}

export type Urls = keyof paths
type Operations<U extends Urls> = paths[U]
export type Methods<U extends Urls> = keyof Operations<U>
type Operation<U extends Urls, M extends Methods<U>> = Operations<U>[M]
type Parameters<U extends Urls, M extends Methods<U>> = Operation<U, M> extends {parameters: {path: infer A}} ? A : {}
type BodyContent<U extends Urls, M extends Methods<U>> =
    Content<Operation<U, M>> extends {'multipart/form-data': Record<string, unknown>}
        ? FormData
        : Content<Operation<U, M>> extends {'application/json': infer A}
            ? A
            : {}
export type ApiBody<U extends Urls, M extends Methods<U>> = Merge<Parameters<U, M>, BodyContent<U, M>>
export type Response<U extends Urls, M extends Methods<U>> =
    Responses<Operation<U, M>> extends {201: {content: {'application/ld+json': infer A}}}
        ? A
        : Responses<Operation<U, M>> extends {200: {content: {'application/ld+json': infer A}}}
            ? A
            : never

export default async function fetchApi<U extends Urls, M extends Methods<U>>(
    url: U,
    method: M,
    body: ApiBody<U, M>
): Promise<Response<U, M>> {
    const init: Omit<RequestInit, 'headers'> & {
        headers: HeadersInit & {'Content-Type'?: string}
    } = {
        headers: {Accept: 'application/ld+json'},
        method: method as string
    }
    if (body instanceof FormData)
        init.body = body
    else {
        init.headers['Content-Type'] = 'application/json'
        if (method !== 'get')
            init.body = JSON.stringify(body)
    }
    let generatedUrl: string = url
    for (const key in body)
        if (generatedUrl.includes(`{${key}}`))
            generatedUrl = generatedUrl.replace(`{${key}}`, body[key] as string)
    const response = await fetch(generatedUrl, init)
    if (response.status === 422)
        throw response
    const json = await response.json() as Response<U, M>
    return json
}
