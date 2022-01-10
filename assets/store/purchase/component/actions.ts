import * as Cookies from '../../../cookie'
import {MutationTypes} from '.'
import type {State as RootState} from '../../index'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import api from '../../../api'
import {useStore} from 'vuex'

export enum ActionTypes {
    LOAD_FAMILIES = 'LOAD_FAMILIES',
    ADD_FAMILIES = 'ADD_FAMILIES'
}

type ActionContext = VuexActionContext<State, RootState>

const FIRST = 0

export const actions = {
    async [ActionTypes.LOAD_FAMILIES](context: ActionContext): Promise<void> {
        console.debug('useStore', useStore())
        console.debug('context', context)

        const response = await api.path('/api/component-families').method('get').create()({}, {
            headers: {Authorization: `Bearer ${String(Cookies.get('token'))}`}
        })
        const apiFamilies = response.data['hydra:member']
        type ComponentFamily = typeof apiFamilies[typeof FIRST]
        type ComponentFamilyInstance = ComponentFamily & {children: ComponentFamilyInstance[]}
        type ComponentFamilyInstances = Record<string, ComponentFamilyInstance>
        const familyInstances: ComponentFamilyInstances = {}

        for (const family of apiFamilies) {
            if (typeof family['@id'] !== 'undefined')
                familyInstances[family['@id']] = {...family, children: []}
        }
        context.commit(MutationTypes.SET_FamilyInstance, familyInstances)
    },
    async [ActionTypes.ADD_FAMILIES](context: ActionContext, payload: {parent: string, code: string, name: string, copperable: boolean, customsCode: string, file: string}): Promise<void> {
        await fetch('http://localhost:8000/api/component-families', {
            body: {
                // eslint-disable-next-line @typescript-eslint/ban-ts-comment
                // @ts-ignore
                code: payload.code,
                copperable: payload.copperable,
                customsCode: payload.customsCode,
                file: payload.file,
                nom: payload.name,
                parent: payload.parent
            },
            headers: {Authorization: `Bearer ${String(Cookies.get('token'))}`, 'Content-Type': 'multipart/form-data'},
            method: 'post'
        })
    }
}

export type Actions = typeof actions
