import type {State} from './state'

export type items = {
    name: string | null
    proportion:  string | null
    delai: number 
    moq:  number 
    poidsCu:  string | null
    reference: string | null
    indice: number 
    prices: string []
}

export type Getters = {
    list: (state: State) => items
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    list: state => ({
    name: state.name,
    proportion: state.proportion,
    delai: state.delai,
    moq: state.moq,
    poidsCu: state.poidsCu,
    reference: state.reference,
    indice: state.indice,
    prices: state.prices
    })
}
