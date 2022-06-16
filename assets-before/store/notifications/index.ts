import {ActionTypes, actions} from './actions'
import type {Actions} from './actions'
import type {Getters} from './getters'
import type {Module} from 'vuex'
import type {Mutations} from './mutation'
import type {RootState} from '../index'
import type {State} from './state'
import {getters} from './getters'
import {mutations, MutationTypes} from './mutation'

export {ActionTypes, Actions, Getters, Mutations, MutationTypes, State}

export function generateNotifications(state: State): Module<State, RootState> {
    return {actions, getters, mutations, namespaced: true, state}
}
