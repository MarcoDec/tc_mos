import {MutationTypes} from './mutations'
import type {State} from './state'
import type {RootState} from '../../index'
import type {ActionContext as VuexActionContext} from 'vuex'

type ActionContext = VuexActionContext<State, RootState>
export enum ActionTypes {
    DODRAG = 'DODRAG',
    STARTY = 'STARTY',
    STARTX = 'STARTX',
    INITDRAG = 'INITDRAG'

}
export const actions = {
    [ActionTypes.DODRAG]({state, getters, commit}: ActionContext, e: MouseEvent): void {
        const newRatio = 1 - (state.startHeight - e.clientY + state.startY) / (getters.containerHeight - 20)
        if (newRatio > 0.1 && newRatio < 0.9) {
            commit(MutationTypes.DRAG, newRatio)
        }
        // console.log('je suis ici',newRatio)
    },

    [ActionTypes.STARTY]({state, commit}: ActionContext, event: MouseEvent): void {
        const startY = event.clientY
        commit(MutationTypes.STARTY, startY)
    },

    [ActionTypes.STARTX]({getters, commit}: ActionContext): void {
        const startX = getters.bottomHeight
        commit(MutationTypes.START_HEIGHT, startX)
    }


}

export type Actions = typeof actions

