import type {State as Price} from '../../componentSupplierPrices/componentSupplierPrice'
export type State = {
    name: string | null
    proportion: string | null
    delai: number
    moq: number
    poidsCu: string | null
    reference: string | null
    indice: number
    prices: string []
    delete: boolean | null
    update: boolean | null
    update2: boolean | null
    id: number
}
export type Response = Omit<State, 'prices'> & {prices: Omit <Price, 'index'>[]}
