import * as Cookies from '../../../cookie'
import {MutationTypes} from '.'
import type {RootState} from '../../index'
import type {State} from '.'
import type {ActionContext as VuexActionContext} from 'vuex'
import {fetchApi} from '../../../api'

export enum ActionTypes {
    LOAD_FAMILIES = 'LOAD_FAMILIES',
    ADD_FAMILIES = 'ADD_FAMILIES'
}

type ActionContext = VuexActionContext<State, RootState>

export const actions = {
    async [ActionTypes.LOAD_FAMILIES]({commit}: ActionContext): Promise<void> {
        const response  = await fetchApi('/api/component-families', {
            method: 'get',
            headers: {'Content-Type': 'application/json', "Authorization": 'Bearer ' + Cookies.get('token')}
        })
        // console.log('f', response );
        const apiFamilies = response['hydra:member']
        console.log('component:', apiFamilies);
        type ComponentFamily = typeof apiFamilies[0]
        type ComponentFamilyInstance = ComponentFamily & {children: ComponentFamilyInstance[]}
        type ComponentFamilyInstances = Record<string,ComponentFamilyInstance>
        const familyInstances: ComponentFamilyInstances = {}
        
        for (const  family  of apiFamilies){
            if(typeof family['@id']!== 'undefined')
            familyInstances[family['@id']]  = {...family, children: []}    
        }
        console.log('familyInstances',familyInstances);
        commit(MutationTypes.SET_FamilyInstance, familyInstances)
    
    },
    async [ActionTypes.ADD_FAMILIES]({commit}: ActionContext, payload:{parent: string , code: string, name:string, copperable: boolean, customsCode: string, file:string}): Promise<void> {
       console.log('Copperable',payload.copperable);
        const response  = await  fetch('http://localhost:8000/api/component-families', {
            method: 'post',
            headers: {'Content-Type': 'multipart/form-data', "Authorization": 'Bearer ' + Cookies.get('token')},
            body: {
                parent: payload.parent,
                code: payload.code,
                nom:payload.name,
                copperable:payload.copperable,
                customsCode: payload.customsCode,
                file:payload.file
            }
        })
        console.log('response',response);

    }
}

export type Actions = typeof actions
