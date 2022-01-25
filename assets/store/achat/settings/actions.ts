import type {DeepReadonly} from '../../../types/types'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import emitter from '../../../emitter'
import {generateSetting} from './setting'

type ActionContext = DeepReadonly<VuexActionContext<State, State>>

export const actions = {
    async load({dispatch}: ActionContext): Promise<void> {
        const response = [
            {
                delete: true,
                name: 'bbb',
                valeur: 'vvv',
                update: true
            },
            {
                delete: true,
                name: 'aaaa',
                valeur: 'xxxx',
                update: true
            },
        ]
        const list = []
        for (const setting of response)
            list.push(dispatch(
                'registerModule',
                {module: generateSetting(setting), path: ['settings', setting.id.toString()]},
                {root: true}
            ))
        await Promise.all(list)
    }
}

export type Actions = typeof actions
