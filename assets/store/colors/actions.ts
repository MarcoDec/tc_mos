import type {State} from '.'
import type {StoreActionContext} from '../actions'

declare type ActionContext = StoreActionContext<State, unknown>

export const actions = {
    async load({dispatch}: ActionContext): Promise<void> {
        const response = await dispatch('fetchApi', {url: '/api/colors'}, {root: true})
        console.debug(response)
    }
}

export declare type Actions = typeof actions
