/* eslint-disable @typescript-eslint/ban-types */
import type {ApiError, ApiResponse, OpErrorType} from 'openapi-typescript-fetch/dist/esm/types'
import {Fetcher} from 'openapi-typescript-fetch'
import type {paths} from './types/openapi'

const api = Fetcher['for']<paths>()

api.configure({
    use: [
        async (url, init, next): Promise<ApiResponse> => {
            init.headers.set('Accept', 'application/ld+json')
            const response = await next(url, init)
            return response
        }
    ]
})

type Mapped<B> = B extends Record<string, unknown> ? B[keyof B] : unknown

type ApiParameters<C> = C extends {
    parameters?: {
        path?: infer P
        query?: infer Q
        body?: infer B
    }
} ? Mapped<B> & P & Q : {}

type ApiApplicationContent<C> = C extends {'multipart/form-data': infer MFD}
    ? MFD
    : C extends {'application/merge-patch+json': infer MPJ}
        ? MPJ
        : C extends {'application/json': infer RB}
            ? RB
            : Record<string, never>

type ApiContent<C> = C extends {content: infer Body} ? ApiApplicationContent<Body> : {}

type ApiBody<OP> = OP extends {requestBody: infer C} ? ApiContent<C> : {}

type ApiOpArgType<OP> = ApiBody<OP> & ApiParameters<OP>

type ApiOpResponseTypes<OP> = OP extends {responses: infer R} ? {
    [S in keyof R]: R[S] extends {schema?: infer Schema}
        ? Schema
        : R[S] extends {content: {'application/ld+json': infer C}}
            ? C
            : S extends 'default'
                ? R[S]
                : unknown;
} : never

type HTTP_OK = 200
type HTTP_CREATED = 200

type _ApiOpReturnType<T> = HTTP_OK extends keyof T ? T[HTTP_OK] : HTTP_CREATED extends keyof T ? T[HTTP_CREATED] : 'default' extends keyof T ? T['default'] : unknown

type ApiOpReturnType<OP> = _ApiOpReturnType<ApiOpResponseTypes<OP>>

type _ApiTypedFetch<OP> = (arg: ApiOpArgType<OP>, init?: RequestInit) => Promise<ApiResponse<ApiOpReturnType<OP>>>

type ApiTypedFetch<OP> = _ApiTypedFetch<OP> & {
    Error: new (error: ApiError) => ApiError & {
        getActualType: () => OpErrorType<OP>
    }
}

type _ApiCreateFetch<OP, Q = never> = [Q] extends [never] ? () => ApiTypedFetch<OP> : (query: Q) => ApiTypedFetch<OP>

type OK = 1
type ApiCreateFetch<M, OP> = M extends 'delete' | 'patch' | 'post' | 'put' ? OP extends {
    parameters: {
        query: infer Q
    }
} ? _ApiCreateFetch<OP, {[K in keyof Q]: OK | true;}> : _ApiCreateFetch<OP> : _ApiCreateFetch<OP>

type Api = Omit<typeof api, 'path'> & {
    path: <P extends keyof paths>(path: P) => {
        method: <M extends keyof paths[P]>(method: M) => {
            create: ApiCreateFetch<M, paths[P][M]>
        }
    }
}

export default api as Api
