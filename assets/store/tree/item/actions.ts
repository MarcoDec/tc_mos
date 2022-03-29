import type {ComputedGetters, State} from '.'
import type {StoreActionContext} from '../..'
import type {Violations} from '../../../types/types'
import type {components} from '../../../types/openapi'
import {generateItem} from '.'

declare type ActionContext = StoreActionContext<State, ComputedGetters>

export const actions = {
    async remove({dispatch, state}: ActionContext): Promise<void> {
        await dispatch(
            'fetchApi',
            {
                body: {id: state.id},
                method: 'delete',
                url: state.url
            },
            {root: true}
        )
        await dispatch('unregisterModule', state.moduleName.split('/'), {root: true})
    },
    async select({commit, dispatch, state}: ActionContext): Promise<void> {
        await dispatch(`${state.parentModuleName}/unselect`, null, {root: true})
        commit('select', true)
    },
    async update({commit, dispatch, state}: ActionContext, body: FormData): Promise<void> {
        body.append('id', state.id.toString())
        if (body.has('parent') && [null, '', '0', 'null'].includes(body.get('parent') as string))
            body['delete']('parent')
        let response = null
        try {
            response = await dispatch(
                'fetchApi',
                {
                    body,
                    method: 'post',
                    url: state.url
                },
                {root: true}
            ) as components['schemas']['ComponentFamily.jsonld-ComponentFamily-read']
        } catch (e) {
            if (e instanceof Response && e.status === 422) {
                const violations = await e.json() as Violations
                commit('violate', violations.violations)
            }
            return
        }
        const updated = {...response}
        if (typeof updated.parent !== 'string')
            updated.parent = '0'
        const moduleName = state.moduleName
        const path = moduleName.split('/')
        const parentPath = state.parentModuleName
        const url = state.baseUrl
        await dispatch('unregisterModule', path, {root: true})
        await dispatch(
            'registerModule',
            {
                module: generateItem(moduleName, parentPath, updated, url),
                path
            },
            {root: true}
        )
    }
}

export declare type Actions = typeof actions
