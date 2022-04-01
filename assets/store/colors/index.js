import {generateColor} from './color'

export function generateColors() {
    return {
        actions: {
            async load({dispatch}) {
                const response = await dispatch('fetchApi', {url: '/api/colors'}, {root: true})
                const colors = []
                for (const color of response['hydra:member']) {
                    colors.push(dispatch('registerModule', {
                        module: generateColor(color),
                        path: ['colors', color.id.toString()]
                    }, {root: true}))
                }
                await Promise.all(colors)
            }
        },
        getters: {
            tableItems: (state, getters, rootState, rootGetters) => fields => {
                const items = []
                for (const id of Object.keys(state))
                    items.push(rootGetters[`colors/${id}/tableItem`](fields))
                return items
            }
        },
        namespaced: true
    }
}
