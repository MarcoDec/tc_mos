import {generateColor} from './color'

export function generateColors() {
    return {
        actions: {
            async create({commit, dispatch}, body) {
                commit('violate')
                let color = null
                try {
                    color = await dispatch('fetchApi', {
                        body,
                        method: 'post',
                        url: '/api/colors'
                    }, {root: true})
                } catch (e) {
                    if (e instanceof Response && e.status === 422) {
                        const violations = await e.json()
                        commit('violate', violations.violations)
                    }
                    return
                }
                await dispatch('unregisterColors')
                await dispatch('registerColor', color)
            },
            async load({dispatch}) {
                await dispatch('unregisterColors')
                const response = await dispatch('fetchApi', {method: 'get', url: '/api/colors'}, {root: true})
                await dispatch('registerColors', response)
            },
            async registerColor({dispatch}, color) {
                await dispatch('registerModule', {
                    module: generateColor(color),
                    path: ['colors', color.id.toString()]
                }, {root: true})
            },
            async registerColors({dispatch}, response) {
                const colors = []
                for (const color of response['hydra:member'])
                    colors.push(dispatch('registerColor', color))
                await Promise.all(colors)
            },
            async search({dispatch}, body) {
                await dispatch('unregisterColors')
                const response = await dispatch('fetchApi', {body, method: 'get', url: '/api/colors'}, {root: true})
                await dispatch('registerColors', response)
            },
            async unregisterColors({dispatch, getters}) {
                const colors = []
                for (const color of getters.colors)
                    colors.push(dispatch('unregisterModule', ['colors', color], {root: true}))
                await Promise.all(colors)
            }
        },
        getters: {
            colors(state) {
                const colors = []
                for (const color of Object.values(state))
                    if (typeof color === 'object' && !Array.isArray(color))
                        colors.push(color.id)
                return colors
            },
            tableItems: (state, getters, rootState, rootGetters) =>
                fields => getters.colors.map(id => rootGetters[`colors/${id}/tableItem`](fields))
        },
        mutations: {
            violate(state, violations = []) {
                state.violations = violations
            }
        },
        namespaced: true,
        state: {violations: []}
    }
}
