export type items = {
    price: number
    quantite: number
    ref: string | null
    id: number
    index: number
}

export type Getters = {

    rowspan: () => number
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {

    rowspan: () => 1
}
