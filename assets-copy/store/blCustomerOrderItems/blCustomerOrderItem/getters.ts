import type {State} from './state'

export type itemsBl = {
    currentPlace: string | null
    departureDate: string | null
    number: number | null
}

export type Getters = {
    list: (state: State) => itemsBl
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    list: state => ({
        currentPlace: state.currentPlace,
        departureDate: state.departureDate,
        number: state.number
    })
}
