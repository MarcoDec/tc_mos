import type {State} from '.'

export type Getters = {
    count: (state: State) => number
    ids: (state: State) => string[]
}

export const getters: Getters = {
    count: state => Object.values(state).filter(({vu}) => vu).length,
    ids: state => Object.keys(state)
}


