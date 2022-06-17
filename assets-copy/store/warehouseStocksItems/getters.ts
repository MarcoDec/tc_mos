import type {Statewarehouse, State as Stocksitems} from './warehouseStocksItem'
import type {State} from '.'

export type Getters = {
    Stocksitems: (state: State) => Statewarehouse[] | null
    Volumeitems: (state: State) => {ref: string | null, quantite: number, type: string | null, delete: boolean | null, update: boolean | null}[]
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    Stocksitems(state) {
        const stocksitems: Statewarehouse[] = []
        let warehouseStocksItems: Stocksitems[] = []
        warehouseStocksItems = Object.values(state)
        warehouseStocksItems.forEach(element => {
            const warehouseStocksItem: Statewarehouse = {
                composantId: element.composant?.id ?? null,
                composantRef: element.composant?.ref ?? null,
                delete: element.deletee,
                id: element.id,
                localisation: element.localisation,
                numeroDeSerie: element.numeroDeSerie,
                prison: element.prison,
                produitId: element.produit?.id ?? null,
                produitRef: element.produit?.ref ?? null,
                quantite: element.quantite,
                update: element.update,
                update2: element.update2
            }
            stocksitems.push(warehouseStocksItem)
        })
        return stocksitems
    },
    Volumeitems(state) {
        const x = 0
        const volumes: Record <string, {ref: string | null, quantite: number, type: string | null, delete: boolean | null, update: boolean | null}> = {}
        for (const volumeitem of Object.values(state)){
            if (volumeitem.composant !== null && volumeitem.composant.id > x){
                if (typeof volumes[volumeitem.composant.id.toString()] === 'undefined'){
                    volumes[volumeitem.composant.id] = {delete: volumeitem.deletee, quantite: volumeitem.quantite, ref: volumeitem.composant.ref, type: 'Composant', update: volumeitem.update}
                } else {
                    volumes[volumeitem.composant.id].quantite += volumeitem.quantite
                }
            } else if (volumeitem.produit !== null && volumeitem.produit.id > x){
                if (typeof volumes[volumeitem.produit.id] === 'undefined'){
                    volumes[volumeitem.produit.id.toString()] = {delete: volumeitem.deletee, quantite: volumeitem.quantite, ref: volumeitem.produit.ref, type: 'Produit', update: volumeitem.update}
                } else {
                    volumes[volumeitem.produit.id].quantite += volumeitem.quantite
                }
            }
        }
        return Object.values(volumes)
    }
}
