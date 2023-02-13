import type {Getters} from './getters'
import type {Module} from 'vuex'
import type {RootState} from '../../../index'
import type {State} from './state'
import {getters} from './getters'

export {Getters, State}
export function generateModule(state: State): Module<State, RootState>{
    return {getters, namespaced: true, state}
}