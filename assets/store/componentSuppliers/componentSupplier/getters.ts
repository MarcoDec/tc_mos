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
    id: number
}

export type Getters = {
    rowspan : (state : State) => number
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    
    rowspan: state => state.prices.length//+1
}
