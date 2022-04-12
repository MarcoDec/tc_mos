import type {ComputedGetters, State} from '.'
import type {StoreActionContext} from '..'

declare type ActionContext = StoreActionContext<State, ComputedGetters>

export const actions = {
    async load({commit}: ActionContext): Promise<void> {
        const needs = [
            {
                id: 1,
                ref: '111444',
                total: '888888'
            },
            {
                id: 2,
                ref: '8888888',
                total: '9999999'
            },
            {
                id: 3,
                ref: '788855',
                total: '4445555544'
            },
            {
                id: 4,
                ref: '111111',
                total: '2266655'
            },
            {
                id: 5,
                ref: '222155444',
                total: '885542221'
            },
            {
                id: 6,
                ref: '11021993',
                total: '33333'
            },
            {
                id: 7,
                ref: '666222277',
                total: '30005599'
            },
            {
                id: 8,
                ref: '999555222',
                total: '02158'
            },
            {
                id: 9,
                ref: '4447777',
                total: '20150000'
            },
            {
                id: 10,
                ref: '3335555',
                total: '7458965'
            },
            {
                id: 11,
                ref: '22225555',
                total: '8888881'
            }
        ]

        commit('needs', needs)
    },
    async show({commit, getters}: ActionContext, infinite: {loaded: () => void, complete: () => void}): Promise<void> {
        commit('show')

        if (getters.hasNeeds) {
            infinite.loaded()
        } else {
            infinite.complete()
        }
    }
}

export declare type Actions = typeof actions
