import type {DeepReadonly} from '../types/types'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import emitter from '../emitter'

type ActionContext = DeepReadonly<VuexActionContext<State, State>>

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
    }
}

export type Actions = typeof actions
