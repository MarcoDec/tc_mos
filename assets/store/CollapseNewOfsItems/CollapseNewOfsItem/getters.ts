import type {State} from './state'

export type items = {
    client: string | null
    cmde: string | null
    debutProd: string | null
    finProd: string | null
    id: number
    minDeLancement: number
    ofsAssocies: string | null
    produit: string | null
    qteDemandee: number
    quantite: number
    quantiteMin: number
    siteDeProduction: string | null
    etatInitialOF: string | null
    lancerOF: boolean | null
}

export type Getters = {
    items: (state: State) => items
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    items: state => ({
        client: state.client,
        cmde: state.cmde,
        debutProd: state.debutProd,
        etatInitialOF: state.etatInitialOF,
        finProd: state.finProd,
        id: state.id,
        lancerOF: state.lancerOF,
        minDeLancement: state.minDeLancement,
        ofsAssocies: state.ofsAssocies,
        produit: state.produit,
        qteDemandee: state.qteDemandee,
        quantite: state.quantite,
        quantiteMin: state.quantiteMin,
        siteDeProduction: state.siteDeProduction
    })
}
