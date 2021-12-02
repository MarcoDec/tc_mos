import type {ActionTree} from 'vuex'
import {MutationTypes} from '.'
import type {RootState} from '../index'
import type {State} from '.'

export enum ActionTypes {
    DRAG = 'DRAG',
    INIT_DRAG = 'INIT_DRAG'
}

export const actions: ActionTree<State, RootState> = {
    async [ActionTypes.DRAG]({commit, getters, state}, e: MouseEvent): Promise<void> {
        commit(MutationTypes.RATIO, 1 - (state.startHeight - e.clientY + state.startY) / (getters.containerHeight - 20))
    },
    async [ActionTypes.INIT_DRAG]({commit, getters}, e: MouseEvent): Promise<void> {
        commit(MutationTypes.INIT_DRAG, {e, startHeight: getters.bottomHeight})
    }
}

export type Actions = typeof actions
