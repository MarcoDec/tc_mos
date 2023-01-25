import type {State} from './setting'

export type Getters = {
    ids: (state: State) => string[]

}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}

export const getters: Getters = {
    ids: state => Object.keys(state),
}
