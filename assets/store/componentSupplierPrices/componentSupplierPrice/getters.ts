import type {State} from './state'

export type items = {
    price: number 
    quantite:  number
    ref: string | null
}

export type Getters = {
    list: (state: State) => items
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    list: state => ({
        price: state.price,
        quantite:  state.quantite,
        ref: state.ref
    })
}
