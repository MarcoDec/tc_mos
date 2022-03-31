export function generateColors() {
    return {
        actions: {
            async load({dispatch}) {
                await dispatch('fetchApi', {url: '/api/colors'}, {root: true})
            }
        },
        namespaced: true
    }
}
