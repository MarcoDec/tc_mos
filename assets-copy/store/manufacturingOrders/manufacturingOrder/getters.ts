import type {State} from './state'

export type Orders = {
    etat: string | null
    id: number | null
}
export type Getters = {
    list: (state: State) => Orders

}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    list: state => ({
        etat: state.etat,
        id: state.id
    })
}
