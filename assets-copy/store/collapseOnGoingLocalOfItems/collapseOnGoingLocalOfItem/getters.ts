import type {State} from './state'

export type LocalOfItems = {
    client: string | null
    cmde: string | null
    debutProd: string | null
    finProd: string | null
    id: number
    of: number
    produit: string | null
    quantite: number
    quantiteProduite: number
    etat: string | null
}

export type Getters = {
    LocalOfItems: (state: State) => LocalOfItems
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    LocalOfItems: state => ({
        client: state.client,
        cmde: state.cmde,
        debutProd: state.debutProd,
        etat: state.etat,
        finProd: state.finProd,
        id: state.id,
        of: state.of,
        produit: state.produit,
        quantite: state.quantite,
        quantiteProduite: state.quantiteProduite
    })
}
