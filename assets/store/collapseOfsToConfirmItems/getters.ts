import type {State} from '.'
import type {State as toConfirmItems} from './collapseOfsToConfirmItem'

export type Getters = {
    toConfirmItems: (state: State) => toConfirmItems[] | null
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    toConfirmItems: state => Object.values(state)
}
