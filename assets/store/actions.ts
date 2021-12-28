import type {DeepReadonly} from '../types/types'
import type {RootState} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'

export enum ActionTypes {
    FETCH_API = 'FETCH_API'
}

type ActionContext = DeepReadonly<VuexActionContext<RootState, RootState>>

export const actions = {
    async [ActionTypes.FETCH_API](context: ActionContext, action: Promise<unknown>): Promise<void> {
        try {
            await action
        } catch (e) {
            console.debug(e)
        }
    }
}

export type Actions = typeof actions
