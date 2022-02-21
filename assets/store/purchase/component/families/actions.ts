import type {ComputedGetters, State} from '.'
import type {StoreActionContext} from '../../..'
import {generateFamily} from './family'

declare type ActionContext = StoreActionContext<State, ComputedGetters>

export const actions = {
    async create({dispatch, state}: ActionContext, body: FormData): Promise<void> {
        const response = await dispatch(
            'fetchApi',
            {
                body,
                method: 'post',
                url: '/api/component-families'
            },
            {root: true}
        )
        const created = {...response}
        if (typeof created.parent !== 'string')
            created.parent = '0'
        await dispatch(
            'registerModule',
            {
                module: generateFamily(
                    `${state.moduleName}/${created.id.toString()}`,
                    state.moduleName,
                    created
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
                url: '/api/component-families'
            },
            {root: true}
        )
        const families: Promise<unknown>[] = [
            dispatch(
                'registerModule',
                {
                    module: generateFamily(
                        `${state.moduleName}/0`,
                        state.moduleName,
                        {
                            '@context': '',
                            '@id': '0',
                            '@type': '',
                            code: 'Familles',
                            id: 0,
                            name: 'Composants'
                        },
                        {opened: true, selected: false}
                    ),
                    path: [state.moduleName, '0']
                },
                {root: true}
            )
        ]
        for (const family of response['hydra:member']) {
            const stateFamily = {...family}
            if (typeof stateFamily.parent !== 'string')
                stateFamily.parent = '0'
            families.push(dispatch(
                'registerModule',
                {
                    module: generateFamily(
                        `${state.moduleName}/${stateFamily.id.toString()}`,
                        state.moduleName,
                        stateFamily
                    ),
                    path: [state.moduleName, stateFamily.id.toString()]
                },
                {root: true}
            ))
        }
        await Promise.all(families)
    },
    async unselect({commit, getters}: ActionContext): Promise<void> {
        for (const family of getters.families)
            commit(`${family}/select`, false, {root: true})
    }
}

export declare type Actions = typeof actions
