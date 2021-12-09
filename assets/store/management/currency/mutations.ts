import type {State} from '.'

export enum MutationTypes {
    GET_CURRENCY = 'GET_CURRENCY',
    FILTER = 'FILTER',


}

export const mutations = {
    [MutationTypes.GET_CURRENCY](state: State, liste: Array<any>): void {
        state.list = liste
    },
    [MutationTypes.FILTER](state: State, {code, nom, active}: { code: string, nom: string, active: boolean }): void {
            state.nom = nom,
            state.code = code,
            state.active = active
    },
}

export type Mutations = typeof mutations
