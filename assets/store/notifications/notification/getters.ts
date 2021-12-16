import type {State} from '.'

export type Getters = {
    color: (state: State) => string
}

export const getters: Getters = {
    color: state => (state.vu ? '#adb5bd5e' : 'none')
}


