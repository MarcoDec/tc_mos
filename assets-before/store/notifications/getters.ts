import type {State} from './notification'

export type Getters = {
    count: (state: State) => number
    ids: (state: State) => string[]
    cat: (state: State) => string[]
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}

export const getters: Getters = {
    count: state => Object.values(state).filter(({read}) => read).length,
    ids: state => Object.keys(state),
    cat: state => Object.values(state)
}
 