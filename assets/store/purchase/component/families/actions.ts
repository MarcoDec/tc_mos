import type {State} from '.'
import type {StoreActionContext} from '../../..'
import {generateFamily} from './family'

type ActionContext = StoreActionContext<State>

export const actions = {
    async create({dispatch}: ActionContext, body: FormData): Promise<void> {
        await dispatch(
            'fetchApi',
            {
                body,
                method: 'post',
                url: '/api/component-families'
            },
            {root: true}
        )
    },
    async load({dispatch}: ActionContext): Promise<void> {
        const response = await dispatch(
            'fetchApi',
            {
                body: {},
                method: 'get',
                url: '/api/component-families'
            },
            {root: true}
        )
        const families = []
        for (const family of response['hydra:member'])
            families.push(dispatch(
                'registerModule',
                {module: generateFamily(family), path: ['families', family.id.toString()]},
                {root: true}
            ))
        await Promise.all(families)
    }
}

export type Actions = typeof actions
