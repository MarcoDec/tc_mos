import type * as Store from '.'
import type {ApiBody, Response as ApiResponse, Methods, Urls} from '../api'
import app from '../app'
import emitter from '../emitter'
import fetchApi from '../api'

declare type ActionContext = Store.ActionContext<Actions, Store.Mutations, Store.State>

export declare type ApiPayload<U extends Urls, M extends Methods<U>> = {
    body: ApiBody<U, M>
    method: M
    url: U
}

declare module 'vuex' {
    export interface Dispatch {
        // eslint-disable-next-line @typescript-eslint/prefer-function-type
        <U extends Urls, M extends Methods<U>>(action: 'fetchApi', payload?: ApiPayload<U, M>, options?: {root: true}): Promise<ApiResponse<U, M>>
    }
}

declare type ModulePath = string[] | string
// eslint-disable-next-line @typescript-eslint/no-explicit-any
declare type ModulePayload = {module: Store.Module<any>, path: ModulePath}

export declare type Actions = {
    fetchApi: <U extends Urls, M extends Methods<U>>(ctx: ActionContext, payload: ApiPayload<U, M>) => Promise<ApiResponse<U, M>>
    registerModule: (ctx: ActionContext, payload: ModulePayload) => Promise<void>
    unregisterModule: (ctx: ActionContext, path: ModulePath) => Promise<void>
}

export const actions: Actions = {
    async fetchApi({commit}, payload) {
        commit('spin')
        try {
            const response = await fetchApi(payload.url, payload.method, payload.body)
            return response
        } catch (e) {
            if (e instanceof Response) {
                if (e.status !== 422) {
                    commit('responseError', {status: e.status, text: await e.json() as string})
                    emitter.emit('error')
                }
            } else {
                commit('error')
                emitter.emit('error')
            }
            throw e
        } finally {
            commit('spin')
        }
    },
    async registerModule(ctx, payload) {
        (app.config.globalProperties.$store as Store.Store).registerModule(payload.path as string, payload.module)
    },
    async unregisterModule(ctx, path) {
        (app.config.globalProperties.$store as Store.Store).unregisterModule(path as string)
    }
}
