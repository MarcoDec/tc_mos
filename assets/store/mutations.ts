import type {State} from '.'

export declare type Mutations = {
    error: (state: State) => void
    responseError: (state: State, {status: responseStatus, text}: {status: number, text: string | null}) => void
    spin: (state: State) => void
}

export const mutations: Mutations = {
    error(state) {
        state.text = 'Une erreur s\'est produite.'
    },
    responseError(state, {status: responseStatus, text}) {
        state.status = responseStatus
        state.text = text
    },
    spin(state) {
        state.spinner = !state.spinner
    }
}
