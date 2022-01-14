import type {State} from '.'

export enum MutationTypes {
    VU = 'VU' ,
}

export const mutations = {
    [MutationTypes.VU](state: State): void {
        state.read = !state.read
    },
}

export type Mutations = typeof mutations
