import type {State as Item} from './item'
import type {Module} from '..'
import type {State} from './state'
import {getters} from './getters'

export declare type RootState = {[K in keyof Item as `tree/{id}/${K}`]: Item[K]}
export type {State}

export function generateTree(moduleName: [string, ...string[]], url: string): Module<State> {
    return {getters, namespaced: true, state: {moduleName: moduleName.join('/'), url, violations: []}}
}
