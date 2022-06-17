import type {ComputedGetters, State} from '.'
import type {Item} from './item'
import type {StoreActionContext} from '..'
import type {Violations} from '../../types/types'
import {generateItem} from './item'
import type {operations} from '../../types/openapi'

declare type ActionContext = StoreActionContext<State, ComputedGetters>

declare type ResponseLoad =
    operations['getComponentFamilyCollection']['responses'][200]['content']['application/ld+json']
    & operations['getProductFamilyCollection']['responses'][200]['content']['application/ld+json']

export const actions = {
    async create({commit, dispatch, state}: ActionContext, body: FormData): Promise<void> {
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
            ) as Item
        } catch (e) {
            if (e instanceof Response && e.status === 422) {
                const violations = await e.json() as Violations
                commit('violate', violations.violations)
            }
            return
        }
        const created = {...response}
        if (typeof created.parent !== 'string')
            created.parent = '0'
        await dispatch(
            'registerModule',
            {
                module: generateItem(
                    `${state.moduleName}/${created.id.toString()}`,
                    state.moduleName,
                    created,
                    state.url
                ),
                path: [state.moduleName, created.id.toString()]
            },
            {root: true}
        )
    },
    async load({dispatch, state}: ActionContext): Promise<void> {
        const response = await dispatch(
            'fetchApi',
            {
                body: {},
                method: 'get',
                url: state.url
            },
            {root: true}
        ) as ResponseLoad
        const items: Promise<unknown>[] = []
        for (const item of response['hydra:member']) {
            const stateItem = {...item}
            if (typeof stateItem.parent !== 'string')
                stateItem.parent = '0'
            items.push(dispatch(
                'registerModule',
                {
                    module: generateItem(
                        `${state.moduleName}/${stateItem.id.toString()}`,
                        state.moduleName,
                        stateItem,
                        state.url
                    ),
                    path: [state.moduleName, stateItem.id.toString()]
                },
                {root: true}
            ))
        }
        await Promise.all(items)
    },
    async unselect({commit, getters}: ActionContext): Promise<void> {
        for (const item of getters.items)
            commit(`${item}/select`, false, {root: true})
    }
}

export declare type Actions = typeof actions
