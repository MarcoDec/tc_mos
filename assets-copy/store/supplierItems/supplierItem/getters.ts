import type {State} from './state'

export type Items = {
    compagnie: string | null
    composant: string | null
    date: string | null
    etat: string | null
    id: number | null
    produit: string | null
    quantite: number | null
    quantiteS: number | null
    ref: string | null
    texte: string | null
    type: string | null
}

export type Getters = {
    list: (state: State) => Items
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    list: state => ({
        compagnie: state.compagnie,
        composant: state.composant,
        date: state.date,
        etat: state.etat,
        id: state.id,
        produit: state.produit,
        quantite: state.quantite,
        quantiteS: state.quantiteS,
        ref: state.ref,
        texte: state.texte,
        type: state.type
    })
}

