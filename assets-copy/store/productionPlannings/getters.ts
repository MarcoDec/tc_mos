import type {State} from '.'
import type {State as apiFields} from './productionPlanning'

export type Getters = {
    apiFields: (state: State) => apiFields[] | null
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    apiFields: state => Object.values(state)
}
