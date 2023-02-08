import {State} from "./setting";

export enum MutationTypes {
    LIST = 'LIST'
}

export const mutations = {
    [MutationTypes.LIST](state: State, list:string[]): void {
        list = state.list
        console.log('QDDDDD---->',list)
    },
}

export type Mutations = typeof mutations
