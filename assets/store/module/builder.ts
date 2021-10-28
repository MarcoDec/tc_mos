import {buildAction, defineActions} from './Action'
import {buildGetters, defineGetters} from './Getter'
import {buildState, defineStates} from './State'
import type Module from './Module'
import type {ModuleState} from './Module'
import store from '../store'

export function defineMembers(module: Module, namespace: string): void {
    defineActions(module, namespace)
    defineGetters(module, namespace)
    defineStates(module, namespace)
}

export function registerModule(state: ModuleState, module: Module): void {
    store.registerModule(state.namespace, {
        actions: buildAction(module),
        getters: buildGetters(module),
        namespaced: true,
        state: buildState(module, state)
    })
}
