import type {State} from './state'

export enum MutationTypes {
    SPINNER = 'SPINNER',
}

export const mutations = {

    [MutationTypes.SPINNER](state:State): void {
       state.spinner = !state.spinner
    },
}

