import type {State as LocalOfItems} from './collapseOnGoingLocalOfItem'
import type {State} from '.'

export type Getters = {
    LocalOfItems: (state: State) => LocalOfItems[] | null
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    LocalOfItems: state => Object.values(state)
}
