import type {State} from '.'


export type Items = {
    composant: string | null
    produit: string | null
    type: string | null
    ref: string | null
    etat: string | null
    texte: string | null
    compagnie: string | null
    date: string | null
    delete: boolean| null
    update: boolean | null
    quantiteS: number | null
    quantite: number | null
    id: number | null
}

export type Getters = {
    items: (state: State) => Items|any
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters= {
    items: state => Object.values(state),
}

