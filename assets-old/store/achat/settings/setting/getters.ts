import type {State} from '.'



export type Getters = {


}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {


}


