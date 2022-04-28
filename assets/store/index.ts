import type {Actions, StoreActionContext} from './actions'
import type {GetterTree, Store, Module as VuexModule} from 'vuex'
import type {Mutations} from './mutation'
import type {State as Security} from './security'
import type {State} from './state'
import type {ComputedGetters as VueComputedGetters} from '../types/vue'
import {actions} from './actions'
import {createStore} from 'vuex'
import {generateSecurity} from './security'
import {mutations} from './mutation'
import {productionPlannings} from './productionPlannings'
import {productionPlanningsItems} from './productionPlanningsItems'
import {CollapseNewOfsItems} from './CollapseNewOfsItems'
import {state} from './state'

export type {Actions, Mutations, State, StoreActionContext}

export function generateStore(security: Security): Store<State> {
    return createStore<State>({
        actions,
        modules: {CollapseNewOfsItems, productionPlannings, productionPlanningsItems, security: generateSecurity(security)},
        mutations,
        state,
        strict: process.env.NODE_ENV !== 'production'
    })
}

export declare type AppStore = ReturnType<typeof generateStore>
export declare type Getters = GetterTree<State, State>
export declare type ComputedGetters<G extends GetterTree<S, State>, S> = VueComputedGetters<G, S, State>
export declare type RootComputedGetters = ComputedGetters<Getters, State>
export declare type Module<S> = VuexModule<S, State>
