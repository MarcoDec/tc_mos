import {fetchApi} from '../../../api'

export const module = {
    actions: {
        async fetchCurrency({commit}) {
            const response = await fetchApi('/api/currencies', {
                method: 'get',
                headers: {'Content-Type': 'application/json', Authorization: `Bearer ${Cookies.get('token')}`}
            })
            const list = response['hydra:member']
            commit('getCurrency', list)
        },
        async updateData({commit}, payload) {
            const id = Cookies.get('idItem')
            if (typeof id === 'undefined')
                return
            const response = await fetch(`/api/currencies/${id}`, {
                body: JSON.stringify({
                    active: payload.active
                }),
                method: 'patch',
                headers: {
                    'Content-Type': ' application/merge-patch+json',
                    Authorization: `Bearer ${Cookies.get('token')}`
                }
            })
        }
    },
    getters: {
        filters: state => ({code: state.code, nom: state.nom, active: state.active}),
        getListCurrency: state => state.list
            .filter(currency => currency.code.toUpperCase().startsWith(state.code.toUpperCase()))
            .filter(currency => (typeof currency.name === 'string' ? currency.name.toUpperCase().startsWith(state.nom.toUpperCase()) : null))
            .filter(currency => currency.active === state.active)
    },
    mutations: {
        filter(state, {code, nom, active}) {
            state.nom = nom,
            state.code = code,
            state.active = active
        },
        getCurrency(state, liste) {
            state.list = liste
        }
    },
    namespaced: true,
    state: {
        active: false,
        code: '',
        list: [],
        nom: ''
    }
}
