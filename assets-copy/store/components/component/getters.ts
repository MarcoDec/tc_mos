import type {State} from './state'

export type Components = {
    etat: string | null
    id: number | null
    nom: string | null
}

export type Getters = {
    list: (state: State) => Components

}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    list: state => ({
        etat: state.etat,
        id: state.id,
        nom: state.nom
    })
}
