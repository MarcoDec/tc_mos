import type {DeepReadonly} from '../../types/types'
import type {State as Fields} from './productionPlanning'
import type {State as RootState} from '..'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {generateField} from './productionPlanning'

type ActionContext = DeepReadonly<VuexActionContext<State, RootState>>

export const actions = {
    async fetchField({dispatch}: ActionContext): Promise<void> {
        const response: Fields[] = [
            {
                label: '201347',
                name: '201347',
                type: 'text'
            },
            {
                label: '201357',
                name: '201357',
                type: 'text'
            },
            {
                label: '201447',
                name: '201447',
                type: 'text'
            },
            {
                label: '201457',
                name: '201457',
                type: 'text'
            },
            {
                label: '201547',
                name: '201547',
                type: 'text'
            },
            {
                label: '201647',
                name: '201647',
                type: 'text'
            },
            {
                label: '201747',
                name: '201747',
                type: 'text'
            },
            {
                label: '201823',
                name: '201823',
                type: 'text'
            },
            {
                label: '201827',
                name: '201827',
                type: 'text'
            },
            {
                label: '201839',
                name: '201839',
                type: 'text'
            },
            {
                label: '201840',
                name: '201840',
                type: 'text'
            }
        ]

        const productionPlannings = []
        for (const field of response)
            productionPlannings.push(dispatch(
                'registerModule',
                {module: generateField(field), path: ['productionPlannings', field.name]},
                {root: true}
            ))
        await Promise.all(productionPlannings)
    }
}

export type Actions = typeof actions
