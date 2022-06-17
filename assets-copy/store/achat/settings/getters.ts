import type {State as Setting} from './setting'

export type Getters = {
    ids: (state: Setting) => string[]

}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}

export const getters: Getters = {
    ids: state => Object.keys(state),
}
