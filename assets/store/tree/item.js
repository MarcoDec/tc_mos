import {get} from 'lodash'

export function generateItem(
    initialModuleName,
    parentModuleName,
    initialItem,
    initialUrl,
    init = {opened: false, selected: false}
) {
    return {
        actions: {
            async remove({dispatch, state}) {
                await dispatch('fetchApi', {
                    body: {id: state.id},
                    method: 'delete',
                    url: state.url
                }, {root: true})
                await dispatch(`${state.parentModuleName}/unselect`, null, {root: true})
                await dispatch('unregisterModule', state.moduleName.split('/'), {root: true})
            },
            async select({commit, dispatch, state}) {
                await dispatch(`${state.parentModuleName}/unselect`, null, {root: true})
                commit('select', true)
            },
            async update({commit, dispatch, state}, body) {
                body.append('id', state.id.toString())
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
                commit('violate')
                const updated = {...response}
                if (typeof updated.parent !== 'string')
                    updated.parent = '0'
                const moduleName = state.moduleName
                const path = moduleName.split('/')
                const parentPath = state.parentModuleName
                const url = state.baseUrl
                await dispatch('unregisterModule', path, {root: true})
                await dispatch('registerModule', {
                    module: generateItem(moduleName, parentPath, updated, url),
                    path
                }, {root: true})
            }
        },
        getters: {
            children(state, computed, rootState, rootGetters) {
                const children = []
                for (const item of rootGetters[`${state.parentModuleName}/items`])
                    if (state['@id'] === get(rootState, `${item}/parent`.split('/')))
                        children.push(get(rootState, `${item}/moduleName`.split('/')))
                return children
            },
            fullName(state, computed, rootState, rootGetters) {
                if (typeof state.parent === 'string' && state.parent !== '0')
                    for (const item of rootGetters[`${state.parentModuleName}/items`])
                        if (state.parent === get(rootState, `${item}/@id`.split('/')))
                            return `${rootGetters[`${item}/fullName`]}/${state.name}`
                return state.name
            },
            hasChildren: (state, computed) => computed.children.length > 0,
            label: state => (typeof state.code === 'string' ? `${state.code} — ${state.name}` : state.name),
            option: (state, computed) => ({text: computed.fullName, value: state['@id']})
        },
        mutations: {
            select(state, selected) {
                state.selected = selected
            },
            toggle(state) {
                if (state.id !== 0)
                    state.opened = !state.opened
            },
            violate(state, violations = []) {
                state.violations = violations
            }
        },
        namespaced: true,
        state: {
            baseUrl: initialUrl,
            moduleName: initialModuleName,
            parentModuleName,
            url: `${initialUrl}/{id}`,
            violations: [], ...initialItem, ...init
        }
    }
}
