import type {Actions, StoreActionContext} from './actions'
import type {GetterTree, Store, Module as VuexModule} from 'vuex'
import type {State as RootState, State} from './state'
import type {Mutations} from './mutation'
import type {State as Security} from './security'
import type {ComputedGetters as VueComputedGetters} from '../types/vue'
import {actions} from './actions'
import {attributs} from './attributs'
import {clients} from './clients'
import {compagnies} from './engines/compagnies'
import {components} from './components'
import {countries} from './countries'
import {createStore} from 'vuex'
import {customers} from './customers'
import {employees} from './employees'
import {engines} from './engines'
import {famillies} from './famillies'
import {famillyProducts} from './famillyProducts'
import {generateSecurity} from './security'
import {groupes} from './engines/groupes'
import {incoterms} from './incoterms'
import {manufacturingOrders} from './manufacturingOrders'
import {mutations} from './mutation'
import {produits} from './produits'
import {state} from './state'
import {suppliers} from './suppliers'

export type {Actions, Mutations, State, StoreActionContext}
export type {RootState}

export function generateStore(security: Security): Store<State> {
    return createStore<State>({
        actions,
        modules: {
            attributs,
            clients,
            compagnies,
            components,
            countries,
            customers,
            employees,
            engines,
            famillies,
            famillyProducts,
            groupes,
            incoterms,
            manufacturingOrders,
            produits,
            security: generateSecurity(security),
            suppliers
        },
        mutations,
        state,
        strict: process.env.NODE_ENV !== 'production'
    })
}

export declare type AppStore = ReturnType<typeof generateStore>
export declare type Getters = GetterTree<State, State>
export declare type ComputedGetters<
    G extends GetterTree<S, State>,
    S
> = VueComputedGetters<G, S, State>
export declare type RootComputedGetters = ComputedGetters<Getters, State>
export declare type Module<S> = VuexModule<S, State>
