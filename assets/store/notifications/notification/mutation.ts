import type {State} from '.'

export enum MutationTypes {
    VU = 'VU'


}

export const mutations = {
    [MutationTypes.VU](state: State): void {
        state.vu = !state.vu
    }


}

export type Mutations = typeof mutations
