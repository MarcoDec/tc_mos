import type {State} from './state'

export type toConfirmItems = {
    client: string | null
    cmde: string | null
    debutProd: string | null
    finProd: string | null
    id: number
    of: number
    produit: string | null
    quantite: number
    quantiteProduite: number
    siteDeProduction: string | null
    confirmerOF: boolean | null
}

export type Getters = {
    toConfirmItems: (state: State) => toConfirmItems
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    toConfirmItems: state => ({
        client: state.client,
        cmde: state.cmde,
        confirmerOF: state.confirmerOF,
        debutProd: state.debutProd,
        finProd: state.finProd,
        id: state.id,
        of: state.of,
        produit: state.produit,
        quantite: state.quantite,
        quantiteProduite: state.quantiteProduite,
        siteDeProduction: state.siteDeProduction
    })
}
