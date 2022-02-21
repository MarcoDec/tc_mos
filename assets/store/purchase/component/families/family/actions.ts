import type {ComputedGetters, State} from '.'
import type {StoreActionContext} from '../../../..'

declare type ActionContext = StoreActionContext<State, ComputedGetters>

export const actions = {
    async select({commit, dispatch, state}: ActionContext): Promise<void> {
        await dispatch(`${state.parentModuleName}/unselect`, null, {root: true})
        commit('select', true)
    },
    async update({dispatch, state}: ActionContext, body: FormData): Promise<void> {
        body.append('id', state.id.toString())
        if (body.has('parent') && body.get('parent') === '0')
            body['delete']('parent')
        await dispatch(
            'fetchApi',
            {
                body,
                method: 'post',
                url: '/api/component-families/{id}'
            },
            {root: true}
        )
    }
}

export declare type Actions = typeof actions
