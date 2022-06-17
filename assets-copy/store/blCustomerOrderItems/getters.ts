import type {State} from '.'
import type {State as itemsBl} from './blCustomerOrderItem'

export type Getters = {
    itemsBl: (state: State) => itemsBl[] | null
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    itemsBl: state => Object.values(state)
}
