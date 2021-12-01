import {MutationTypes} from '.'
import type {RootState} from '../index'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'

export enum ActionTypes {
    DRAG = 'DRAG'
}

type ActionContext = VuexActionContext<State, RootState>

export const actions = {
    async [ActionTypes.DRAG]({commit, getters, state}: ActionContext, e: MouseEvent): Promise<void> {
        commit(MutationTypes.RATIO, 1 - (state.startHeight - e.clientY + state.startY) / (getters.containerHeight - 20))
    }
}

export type Actions = typeof actions
