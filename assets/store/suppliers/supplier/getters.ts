import type {State} from './state'

export type Suppliers = {
    etat: string | null
    nom: string | null
    id: number | null
}

export type Getters = {

}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
}