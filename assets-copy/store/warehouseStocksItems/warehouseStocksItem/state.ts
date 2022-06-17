export type State = {
    composant: {ref: string | null, id: number} | null
    produit: {ref: string | null, id: number} | null
    numeroDeSerie: string | null
    localisation: string | null
    quantite: number
    prison: boolean | null
    deletee: boolean | null
    update: boolean | null
    update2: boolean | null
    id: number
}
export type Statewarehouse = {
    composantRef: string | null
    composantId: number | null
    produitRef: string | null
    produitId: number | null
    numeroDeSerie: string | null
    localisation: string | null
    quantite: number
    prison: boolean | null
    delete: boolean | null
    update: boolean | null
    update2: boolean | null
    id: number
}
