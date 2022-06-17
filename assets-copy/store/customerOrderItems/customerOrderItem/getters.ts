import type {State} from './state'

export type Items = {
    dateLivraisonConfirmée: string | null
    dateLivraisonSouhaitée: string | null
    description: string | null
    etat: string | null
    produit: string | null
    quantitéConfirmée: number | null
    quantitéSouhaitée: number | null
    ref: string | null
}

export type Getters = {
    list: (state: State) => Items
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    list: state => ({
        dateLivraisonConfirmée: state.dateLivraisonConfirmée,
        dateLivraisonSouhaitée: state.dateLivraisonSouhaitée,
        description: state.description,
        etat: state.etat,
        produit: state.produit,
        quantitéConfirmée: state.quantitéConfirmée,
        quantitéSouhaitée: state.quantitéSouhaitée,
        ref: state.ref
    })
}
