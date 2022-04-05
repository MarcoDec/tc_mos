import {generateItem} from './item'
import {get} from 'lodash'

export function generateTree(moduleName, url) {
    return {
        actions: {
            async create({commit, dispatch, state}, body) {
                if (body.has('parent') && [null, '', '0', 'null'].includes(body.get('parent')))
                    body['delete']('parent')
                let response = null
                try {
                    response = await dispatch('fetchApi', {
                        body,
                        method: 'post',
                        url: state.url
                    }, {root: true})
                } catch (e) {
                    if (e instanceof Response && e.status === 422) {
                        const violations = await e.json()
                        commit('violate', violations.violations)
                    }
                    return
                }
                const created = {...response}
                if (typeof created.parent !== 'string')
                    created.parent = '0'
                await dispatch('registerModule', {
                    module: generateItem(`${state.moduleName}/${created.id.toString()}`, state.moduleName, created, state.url),
                    path: [state.moduleName, created.id.toString()]
                }, {root: true})
            },
            async load({dispatch, state}) {
                const response = await dispatch('fetchApi', {
                    body: {},
                    method: 'get',
                    url: state.url
                }, {root: true})
                const items = []
                for (const item of response['hydra:member']) {
                    const stateItem = {...item}
                    if (typeof stateItem.parent !== 'string')
                        stateItem.parent = '0'
                    items.push(dispatch('registerModule', {
                        module: generateItem(`${state.moduleName}/${stateItem.id.toString()}`, state.moduleName, stateItem, state.url),
                        path: [state.moduleName, stateItem.id.toString()]
                    }, {root: true}))
                }
                await Promise.all(items)
            },
            async unselect({commit, getters}) {
                for (const item of getters.items)
                    commit(`${item}/select`, false, {root: true})
            }
        },
        getters: {
            items(state) {
                const items = []
                for (const item of Object.values(state))
                    if (typeof item === 'object' && !Array.isArray(item))
                        items.push(item.moduleName)
                return items
            },
            options(state, computed, rootState, rootGetters) {
                const options = [{text: '', value: null}]
                for (const item of computed.items)
                    options.push(rootGetters[`${item}/option`])
                return options.sort((a, b) => a.text.localeCompare(b.text))
            },
            selected(state, computed, rootState) {
                for (const item of computed.items)
                    if (get(rootState, `${item}/selected`.split('/')))
                        return item
                return null
            }
        },
        mutations: {
            violate(state, violations = []) {
                state.violations = violations
            }
        },
        namespaced: true,
        state: {moduleName: moduleName.join('/'), url, violations: []}
    }
}
