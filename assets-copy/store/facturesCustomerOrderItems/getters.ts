import type {State as ItemsFactures} from './facturesCustomerOrderItem'
import type {State} from '.'

export type Getters = {
    ItemsFactures: (state: State) => ItemsFactures[] | null
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    ItemsFactures: state => Object.values(state)
}
