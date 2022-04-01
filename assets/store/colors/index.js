import {generateColor} from './color'

export function generateColors() {
    return {
        actions: {
            async load({dispatch}) {
                await dispatch('unregisterColors')
                const response = await dispatch('fetchApi', {method: 'get', url: '/api/colors'}, {root: true})
                await dispatch('registerColors', response)
            },
            async registerColors({dispatch}, response) {
                const colors = []
                for (const color of response['hydra:member']) {
                    colors.push(dispatch('registerModule', {
                        module: generateColor(color),
                        path: ['colors', color.id.toString()]
                    }, {root: true}))
                }
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
            colors: state => Object.keys(state),
            tableItems: (state, getters, rootState, rootGetters) => fields => {
                const items = []
                for (const id of getters.colors)
                    items.push(rootGetters[`colors/${id}/tableItem`](fields))
                return items
            }
        },
        namespaced: true
    }
}
