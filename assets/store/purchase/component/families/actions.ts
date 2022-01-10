import type {State as RootState} from '../../..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import api from '../../../../api'
import type {components} from '../../../../types/openapi'

type ActionContext = VuexActionContext<State, RootState>

export const actions = {
    async create(context: ActionContext, family: components['schemas']['ComponentFamily-ComponentFamily-write']): Promise<void> {
        await api.path('/api/component-families').method('post').create()(family)
    },
    async load({dispatch}: ActionContext): Promise<void> {
        const response = await api.path('/api/component-families').method('get').create()({})
        const families = []
        for (const family of response.data['hydra:member'])
            families.push(dispatch(
                'registerModule',
                {module: family, path: ['families', family.id.toString()]},
                {root: true}
            ))
        await Promise.all(families)
    }
}

export type Actions = typeof actions
