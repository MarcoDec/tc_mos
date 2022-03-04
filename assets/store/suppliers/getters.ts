import type {State} from '.'

export type Suppliers = {
    etat: string | null
    nom: string | null
    id: number | null
}

export type Getters = {
    items: (state: State) => Suppliers[] | null

}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    items: state => Object.values(state)
}