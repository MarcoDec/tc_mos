import type {State} from '.'

export enum MutationTypes {
    SET_COMPONENT_FAMILIES = 'SET_COMPONENT_FAMILIES'
}

export const mutations = {
    [MutationTypes.SET_COMPONENT_FAMILIES](state: State, ComponentFamiliesInfo: Array<{ pathid: string | null ; id: number | null; code: string | null; copperable: boolean | null; customsCode: string | null ; filepath: string | null; name: string; parent: string | null ;  }>): void {
        state.ComponentFamiliesInfo = ComponentFamiliesInfo
         console.log( "mutation", state.ComponentFamiliesInfo);
    }
}

export type Mutations = typeof mutations
