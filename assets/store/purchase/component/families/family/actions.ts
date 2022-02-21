import type {ComputedGetters, State} from '.'
import type {StoreActionContext} from '../../../..'
import {generateFamily} from '.'

declare type ActionContext = StoreActionContext<State, ComputedGetters>

export const actions = {
    async select({commit, dispatch, state}: ActionContext): Promise<void> {
        await dispatch(`${state.parentModuleName}/unselect`, null, {root: true})
        commit('select', true)
    },
    async update({dispatch, state}: ActionContext, body: FormData): Promise<void> {
        body.append('id', state.id.toString())
        if (body.has('parent') && [null, '', '0', 'null'].includes(body.get('parent') as string))
            body['delete']('parent')
        const updated = await dispatch(
            'fetchApi',
            {
                body,
                method: 'post',
                url: '/api/component-families/{id}'
            },
            {root: true}
        )
        if (typeof updated.parent !== 'string')
            updated.parent = '0'
        const moduleName = state.moduleName
        const path = moduleName.split('/')
        const parentPath = state.parentModuleName
        await dispatch('unregisterModule', path, {root: true})
        await dispatch(
            'registerModule',
            {
                module: generateFamily(
                    moduleName,
                    parentPath,
                    updated
                ),
                path
            },
            {root: true}
        )
    }
}

export declare type Actions = typeof actions
