import type {AppStore, State} from '.'
import type {Module, ActionContext as VuexActionContext} from 'vuex'
import type {DeepReadonly} from '../types/types'
import app from '../app'
import emitter from '../emitter'

export type StoreActionContext<S> = VuexActionContext<S, State>
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
    async registerModule(context: ActionContext, payload: ModulePayload): Promise<void>{
        (app.config.globalProperties.$store as AppStore)
            .registerModule(payload.path as string, payload.module)
    }
}

export type Actions = typeof actions
