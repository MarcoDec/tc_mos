import type {State} from './state'

export type Customers = {
    etat: string | null
    id: number | null
    nom: string | null
}

export type Getters = {
    list: (state: State) => Customers

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
