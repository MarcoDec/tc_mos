import type {State} from './state'

export type items = {
    composant: {ref: string | null, id: number} | null
    produit: {ref: string | null, id: number} | null
    numeroDeSerie: string | null
    localisation: string | null
    quantité: number
    prison: boolean | null
}

export type Getters = {
    list: (state: State) => items
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    list: state => ({
        composant: state.composant,
        localisation: state.localisation,
        numeroDeSerie: state.numeroDeSerie,
        prison: state.prison,
        produit: state.produit,
        quantité: state.quantite
    })
}
