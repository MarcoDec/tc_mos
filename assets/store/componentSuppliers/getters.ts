import type {State} from '.'
import type {State as items} from './componentSupplier'

export type Getters = {
    items: (state: State) => items[] | null
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    items: state => Object.values(state)
}
