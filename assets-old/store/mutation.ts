import type {State} from './state'

export const mutations = {
    error(state: State): void {
        state.text = 'Une erreur s\'est produite.'
    },
    responseError(state: State, {status: responseStatus, text}: {status: number, text: string | null}): void {
        state.status = responseStatus
        state.text = text
    },
    spin(state: State): void {
        state.spinner = !state.spinner
    }
}

export type Mutations = typeof mutations
