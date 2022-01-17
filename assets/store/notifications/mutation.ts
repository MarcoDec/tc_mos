import {State} from "./notification";

export enum MutationTypes {
    LIST = 'LIST'
}

export const mutations = {
    [MutationTypes.LIST](state: State, list:string[]): void {
        list = state.list
        console.log('jjjjjjjj---->',list)
    },
}

export type Mutations = typeof mutations
