import type {State} from './state'

export type items = {
    id: number
    lien: string | null
    produit: string | null
    ind: string | null
    client: string | null
    stocks: string | null
    vp: string | null
    retard: string | null
}

export type Getters = {
    listItems: (state: State) => items
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    listItems: state => ({
        client: state.client,
        id: state.id,
        ind: state.ind,
        lien: state.lien,
        produit: state.produit,
        retard: state.retard,
        stocks: state.stocks,
        vp: state.vp
    })
}
