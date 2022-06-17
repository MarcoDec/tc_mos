import type {State} from '.'
import type {State as itemsOf} from './ofCustomerOrderItem'

export type Getters = {
    itemsOf: (state: State) => itemsOf[] | null
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    itemsOf: state => Object.values(state)
}
