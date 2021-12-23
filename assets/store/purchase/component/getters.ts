/* eslint-disable @typescript-eslint/ban-ts-comment */
import type {State} from '.'
import clone from 'clone'
import type {components} from '../../../types/openapi'

type ComponentFamily = components ['schemas'] ['ComponentFamily.jsonld-ComponentFamily-read']
type ComponentFamilyInstance = ComponentFamily & {children: ComponentFamilyInstance[]}
type ComponentFamilyRelation = Omit<ComponentFamilyInstance, 'children' | 'parent'> & {
    children: ComponentFamilyRelation[]
    parent?: ComponentFamilyRelation | string | null
    pathid: string
}

export const getters = {
    treeFamilies: (state: State): ComponentFamilyRelation[] => {
        const familyInstances = clone(state.families)
        const relations: ComponentFamilyRelation[] = []
        for (const family of Object.values(familyInstances)) {
            const relation: ComponentFamilyRelation = {...family, pathid: family['@id']}
            if (typeof family.parent !== 'undefined' && family.parent !== null) {
                // @ts-ignore
                const parent: ComponentFamilyRelation = {...familyInstances[family.parent]}
                parent.children.push(relation)
                relation.parent = parent
            } else
                relation.parent = null
            relations.push(relation)
        }
        console.log('relations', relations)
        const mappedFamilies: ComponentFamilyRelation[] = []
        for (const relation of relations) {
            if (relation.parent === null)
                mappedFamilies.push(relation)
        }
        console.log('mappedFamilies', mappedFamilies)
        // @ts-ignore
        return {children: mappedFamilies}
    }
}

export type Getters = typeof getters
