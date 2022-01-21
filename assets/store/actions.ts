import type {ApiBody, Response as ApiResponse, Methods, Urls} from '../api'
import type {AppStore, State} from '.'
import type {DispatchOptions, Module, ActionContext as VuexActionContext} from 'vuex'
import type {DeepReadonly} from '../types/types'
import app from '../app'
import emitter from '../emitter'
import fetchApi from '../api'

type ApiPayload<U extends Urls, M extends Methods<U>> = {
    body: ApiBody<U, M>
    method: M
    multipart: boolean
    url: U
}

declare module 'vuex' {
    export interface Dispatch {
        // eslint-disable-next-line @typescript-eslint/prefer-function-type
        <U extends Urls, M extends Methods<U>>(action: 'fetchApi', payload?: ApiPayload<U, M>, options?: DispatchOptions): Promise<ApiResponse<U, M>>
    }
}

export type StoreActionContext<S> = VuexActionContext<S, State>

type ActionContext = StoreActionContext<State>

type ModulePayload = DeepReadonly<{module: Module<unknown, State>, path: string[] | string}>

export const actions = {
    async fetchApi<U extends Urls, M extends Methods<U>>({commit}: ActionContext, payload: ApiPayload<U, M>): Promise<ApiResponse<U, M>> {
        commit('spin')
        try {
            const response = await fetchApi(payload.url, payload.method, payload.body, payload.multipart)
            return response
        } catch (e) {
            if (e instanceof Response) {
                commit('responseError', {status: e.status, text: await e.json() as string})
            } else
                commit('error')
            emitter.emit('error')
            throw new Error('Erreur de communication avec l\'API')
        } finally {
            commit('spin')
        }
    },
    async registerModule(context: ActionContext, payload: ModulePayload): Promise<void> {
        (app.config.globalProperties.$store as AppStore)
            .registerModule(payload.path as string, payload.module as Module<unknown, State>)
    }
}

export type Actions = typeof actions
