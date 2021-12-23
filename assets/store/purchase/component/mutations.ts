import type {State} from '.'

export enum MutationTypes {
    SET_COMPONENT_FAMILIES = 'SET_COMPONENT_FAMILIES',
    SET_FamilyInstance = 'SET_FamilyInstance',
    SET_ComponentFamilyInstances = 'SET_ComponentFamilyInstances'
}

export const mutations = {
    [MutationTypes.SET_COMPONENT_FAMILIES](state: State, ComponentFamiliesInfo: {pathid: string | null, id: number | null, code: string | null, copperable: boolean | null, customsCode: string | null, filepath: string | null, name: string, parent: string | null}[]): void {
        state.ComponentFamiliesInfo = ComponentFamiliesInfo
    },
    [MutationTypes.SET_FamilyInstance](state: State, families: object): void {
        state.families = families
    }
}

export type Mutations = typeof mutations
