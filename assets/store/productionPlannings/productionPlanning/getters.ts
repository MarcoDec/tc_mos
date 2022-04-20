import type {State} from './state'

export type apiFields = {
    label: string | null
    name: string | null
    type: string | null
}

export type Getters = {
    listFields: (state: State) => apiFields
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    listFields: state => ({
        label: state.label,
        name: state.name,
        type: state.type
    })
}
