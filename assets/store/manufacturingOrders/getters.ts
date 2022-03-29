import type {State} from '.'
import type {ComputedGetters as VueComputedGetters} from '..'

export type Orderd = {
    etat: string | null
    commande: string
    compagnie: string
    id: number
    indice: string
    quantite: string
    quantiteF: string
    numero: string
    produit: string
    usine: string
}

export declare type Getters = {
    items: (state: State) => Orderd[]
}
export declare type ComputedGetters = VueComputedGetters<Getters, State>

export const getters: Getters = {
    items(state) {
        const items = []
        for (const item of Object.values(state))
            if (typeof item === 'object' && !Array.isArray(item)) items.push(item)
        return items
    }
}
