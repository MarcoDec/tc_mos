import type {State as Items} from './customerOrderItem'
import type {State} from '.'

export type Getters = {
    items: (state: State) => Items[] | null
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    items: state => Object.values(state)
}
