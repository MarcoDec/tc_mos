import type {State} from './state'


export type Items = {
    composant: string | null
    produit: string | null
    type: string | null
    ref: string | null
    etat: string | null
    texte: string | null
    compagnie: string | null
    date: string | null
    quantiteS: number | null
    quantite: number | null
    id: number
}

export type Getters = {
    list: (state: State) => Items
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    list: state => ({
        composant: state.composant,
        produit:state.produit,
        type:state.type,
        ref:state.ref,
        etat:state.etat,
        texte:state.texte,
        compagnie:state.compagnie,
        date:state.date,
        quantiteS:state.quantiteS,
        quantite:state.quantite,
        id:state.id,
    })
}

