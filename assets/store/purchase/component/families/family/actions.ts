import type {ComputedGetters, State} from '.'
import type {StoreActionContext} from '../../../..'

declare type ActionContext = StoreActionContext<State, ComputedGetters>

export const actions = {
    async select({commit, dispatch, state}: ActionContext): Promise<void> {
        await dispatch(`${state.parentModuleName}/unselect`, null, {root: true})
        commit('select', true)
    }
}

export declare type Actions = typeof actions
