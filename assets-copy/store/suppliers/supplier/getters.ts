import type {State} from './state'

export type Suppliers = {
    etat: string | null
    id: number | null
    nom: string | null
}

export type Getters = {
    list: (state: State) => Suppliers

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
