import * as Cookies from '../cookie'

export function generateSecurity(initialState) {
    return {
        actions: {
            async login({commit, dispatch}, {password, username}) {
                const user = await dispatch('fetchApi', {
                    body: {
                        password: typeof password === 'string' && password.length > 0 ? password : '',
                        username: typeof username === 'string' && username.length > 0 ? username : '' !== null
                    },
                    method: 'post',
                    url: '/api/login'
                }, {root: true})
                Cookies.set(user.id, user.token)
                commit('login', user)
            },
            async logout({commit, dispatch}) {
                await dispatch('fetchApi', {
                    body: {},
                    method: 'post',
                    url: '/api/logout'
                }, {root: true})
                Cookies.remove()
                commit('login', {})
            }
        },
        getters: {
            has: state => role => state.roles?.includes(role) ?? false,
            hasUser: state => typeof state.username !== 'undefined',
            isItAdmin: (state, computed) => computed.has('ROLE_IT_ADMIN'),
            isProductionAdmin: (state, computed) => computed.has('ROLE_PRODUCTION_ADMIN'),
            isProductionReader: (state, computed) => computed.isProductionAdmin || computed.has('ROLE_PRODUCTION_READER'),
            isProductionWriter: (state, computed) => computed.isProductionAdmin || computed.has('ROLE_PRODUCTION_WRITER'),
            isPurchaseAdmin: (state, computed) => computed.has('ROLE_PURCHASE_ADMIN'),
            isPurchaseReader: (state, computed) => computed.isPurchaseAdmin || computed.has('ROLE_PURCHASE_READER'),
            isPurchaseWriter: (state, computed) => computed.isPurchaseAdmin || computed.has('ROLE_PURCHASE_WRITER')
        },
        mutations: {
            login(state, user) {
                state.roles = user.roles
                state.username = user.username
            }
        },
        namespaced: true,
        state: initialState
    }
}
