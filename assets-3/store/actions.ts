import type {Actions} from 'vuex'

export const actions: Actions = {
    async fetchApi({commit}) {
        commit('spin')
    }
}
