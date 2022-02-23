import type {State} from './state'

export type itemsBl = {
    famille: string | null
    name: string | null
}

export type Getters = {
    list: (state: State) => itemsBl
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    list: state => ({
        famille: state.famille,
        name: state.name
    })
}
