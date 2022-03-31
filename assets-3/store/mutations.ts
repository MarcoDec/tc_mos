import type {Mutations} from 'vuex'

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
