import type {State} from './state'

export type itemsOf = {
    currentPlace: string | null
    deliveryDate: string | null
    manufacturingCompany: string | null
    manufacturingDate: string | null
    ofnumber: number | null
    quantity: number | null
    quantityDone: number | null
}

export type Getters = {
    list: (state: State) => itemsOf
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    list: state => ({
        currentPlace: state.currentPlace,
        deliveryDate: state.deliveryDate,
        manufacturingCompany: state.manufacturingCompany,
        manufacturingDate: state.manufacturingDate,
        ofnumber: state.ofnumber,
        quantity: state.quantity,
        quantityDone: state.quantityDone
    })
}
