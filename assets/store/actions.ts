import type {ApiBody, Response as ApiResponse, Methods, Urls} from '../api'
import type {AppStore, Module, RootComputedGetters, State} from '.'
import type {DispatchOptions, ActionContext as VuexActionContext} from 'vuex'
import app from '../app'
import emitter from '../emitter'
import fetchApi from '../api'

declare type ApiPayload<U extends Urls, M extends Methods<U>> = {
    body: ApiBody<U, M>
    method: M
    url: U
}

declare module 'vuex' {
    export interface Dispatch {
        // eslint-disable-next-line @typescript-eslint/prefer-function-type
        <U extends Urls, M extends Methods<U>>(action: 'fetchApi', payload?: ApiPayload<U, M>, options?: DispatchOptions): Promise<ApiResponse<U, M>>
    }
}

export declare type StoreActionContext<S, G> = Omit<VuexActionContext<S, State>, 'getters'> & {getters: G}

declare type ActionContext = StoreActionContext<State, RootComputedGetters>

// eslint-disable-next-line @typescript-eslint/no-explicit-any
declare type ModulePayload = {module: Module<any>, path: string[] | string}

export const actions = {
    async fetchApi<U extends Urls, M extends Methods<U>>({commit}: ActionContext, payload: ApiPayload<U, M>): Promise<ApiResponse<U, M>> {
        commit('spin')
        try {
            const response = await fetchApi(payload.url, payload.method, payload.body)
            return response
        } catch (e) {
            if (e instanceof Response) {
                if (e.status !== 422) {
                    commit('responseError', {status: e.status, text: await e.json() as string})
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
    async registerModule(context: ActionContext, payload: ModulePayload): Promise<void> {
        (app.config.globalProperties.$store as AppStore)
            .registerModule(payload.path as string, payload.module)
    },
    async unregisterModule(context: ActionContext, path: string[] | string): Promise<void> {
        (app.config.globalProperties.$store as AppStore).unregisterModule(path as string)
    }
}

export declare type Actions = typeof actions
