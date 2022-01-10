import type {Module, ActionContext as VuexActionContext} from 'vuex'
import type {DeepReadonly} from '../types/types'
import type {State} from '.'
import emitter from '../emitter'
import {useStore} from 'vuex'

type ActionContext = DeepReadonly<VuexActionContext<State, State>>

type ModulePayload = {module: Module<unknown, State>, path: string[] | string}

export const actions = {
    async fetchApi({commit}: ActionContext, action: () => Promise<unknown>): Promise<void> {
        commit('spin')
        try {
            await action()
        } catch (e) {
            if (e instanceof Response) {
                commit('responseError', {status: e.status, text: await e.json() as string})
            } else
                commit('error')
            emitter.emit('error')
        } finally {
            commit('spin')
        }
    },
    async registerModule(context: ActionContext, payload: ModulePayload): Promise<void> {
        useStore().registerModule(payload.path as string, payload.module)
    }
}

export type Actions = typeof actions
