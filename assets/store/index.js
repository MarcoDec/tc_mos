import VuexORM from '@vuex-orm/core'
import app from '../app'
import {createStore} from 'vuex'
import emitter from '../emitter'
import fetchApi from '../api'
import {generateSecurity} from './security'

export function generateStore(security) {
    return createStore({
        actions: {
            async fetchApi({commit}, payload) {
                commit('spin')
                try {
                    const response = await fetchApi(payload.url, payload.method, payload.body)
                    return response
                } catch (e) {
                    if (e instanceof Response) {
                        if (e.status !== 422) {
                            commit('responseError', {status: e.status, text: await e.json()})
                            emitter.emit('error')
                        }
                    } else {
                        commit('error')
                        emitter.emit('error')
                    }
                    throw e
                } finally {
                    commit('spin')
                }
            },
            async registerModule(context, payload) {
                app.config.globalProperties.$store.registerModule(payload.path, payload.module)
            },
            async unregisterModule(context, path) {
                app.config.globalProperties.$store.unregisterModule(path)
            }
        },
        modules: {security: generateSecurity(security)},
        mutations: {
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
        },
        plugins: [VuexORM.install()],
        state: {
            spinner: false,
            status: 0,
            text: null
        },
        strict: process.env.NODE_ENV !== 'production'
    })
}
