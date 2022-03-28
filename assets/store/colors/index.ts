import type {Actions} from './actions'
import type {Module} from '../index'
import type {State} from './state'
import {actions} from './actions'

export type {Actions, State}

export function generateColors(): Module<State> {
    return {actions, namespaced: true}
}
