import {MutationTypes} from '.'
import type {RootState} from '../index'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'

export enum ActionTypes {
    DRAG = 'DRAG'
}

type ActionContext = VuexActionContext<State, RootState>

export const actions = {
    async [ActionTypes.DRAG]({commit, getters, state}: ActionContext): Promise<void> {
        function drag({y}: MouseEvent): void {
            commit(MutationTypes.RATIO, (getters.innerHeight - y + state.marginTop) / getters.innerHeight)
        }

        function stopDrag(): void {
            commit(MutationTypes.DISABLE_DRAG)
            document.documentElement.removeEventListener('mousemove', drag)
            document.documentElement.removeEventListener('mouseup', stopDrag)
        }

        document.documentElement.addEventListener('mousemove', drag)
        document.documentElement.addEventListener('mouseup', stopDrag)
    }
}

export type Actions = typeof actions
