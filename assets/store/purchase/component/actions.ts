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
type family = {parent: string , code: string, nom:string, copperable: boolean, customsCode: string, file:string}

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
        type ComponentFamilyRelation = Omit<ComponentFamilyInstance, 'children' | 'parent'> & {
            children: ComponentFamilyRelation[]
            parent?: ComponentFamilyRelation | string | null
        }
        const relations: ComponentFamilyRelation[] = []
        for (const family of Object.values(familyInstances)) {
            const relation: ComponentFamilyRelation = {...family}
            if (typeof family.parent !== 'undefined' && family.parent !== null) {
                const parent: ComponentFamilyRelation = {...familyInstances[family.parent]}
                parent.children.push(relation)
                relation.parent = parent
            } else
                relation.parent = null
            relations.push(relation)
        }
        console.log('relations',relations);
        const mappedFamilies:ComponentFamilyRelation[] = []
        for (const relation of relations){
            if (relation.parent === null)
            mappedFamilies.push(relation)
        }
        console.log('mappedFamilies', mappedFamilies);
        commit(MutationTypes.SET_COMPONENT_FAMILIES, mappedFamilies)
    },
    async [ActionTypes.ADD_FAMILIES]({commit}: ActionContext, {parent, code, nom, copperable, customsCode, file}: family): Promise<void> {
        // const response  = await fetchApi('/api/component-families', {
        //     // json: {parent: parent ?? '', code: code ?? '', nom: nom ??, checked: checked ?? false ,codeDouanier: codeDouanier ?? '' },
        //     method: 'post',
        //     headers: {'Content-Type': 'multipart/form-data', "Authorization": 'Bearer ' + Cookies.get('token')}
            

        // })
        console.log('response');
    }
}

export type Actions = typeof actions
