import type {State} from '.'

export enum MutationTypes {
    VU = 'VU' ,
    LIST = 'LIST' ,
}

export const mutations = {
    [MutationTypes.VU](state: State): void {
        state.read = !state.read
    },
}

export type Mutations = typeof mutations
