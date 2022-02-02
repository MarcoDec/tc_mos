import type {GettersValues, State} from '.'
import type {DeepReadonly} from '../../types/types'
import {MutationTypes} from '.'
import type {RootState} from '../index'
import type {ActionContext as VuexActionContext} from 'vuex'

export enum ActionTypes {
    DRAG = 'DRAG'
}

type ActionContext = DeepReadonly<Omit<VuexActionContext<State, RootState>, 'getters'> & {getters: GettersValues}>

export const actions = {
    async [ActionTypes.DRAG]({commit, getters, state}: ActionContext): Promise<void> {
        function drag({y}: Readonly<MouseEvent>): void {
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
