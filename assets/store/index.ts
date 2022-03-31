import type * as Types from '../types/vue'
import type * as Vuex from 'vuex'
import type {Actions, ApiPayload} from './actions'
import type {Response as ApiResponse, Methods, Urls} from '../api'
import type {Merge, ObjectToIntersection} from '../types/types'
import type {GetterTree} from 'vuex'
import type {Mutations} from './mutations'
import type {State as Security} from './security'
import type {State} from './state'
import {actions} from './actions'
import {createStore} from 'vuex'
import {generateSecurity} from './security'
import {mutations} from './mutations'
import {state} from './state'

export type {Actions, Mutations, State}

export declare type ActionContext<A extends object, M extends object, S> =
    Omit<Vuex.ActionContext<S, State>, 'commit' | 'dispatch'>
    & {
        commit: ObjectToIntersection<Types.Commit<M, S>>
        dispatch: ObjectToIntersection<Types.Dispatch<A, S>>
    }
export declare type ActionContextTree<A extends object, M extends object, S, AT extends object> =
    Omit<Vuex.ActionContext<S, State>, 'commit' | 'dispatch'>
    & {
        commit: ObjectToIntersection<Types.Commit<M, S>>
        dispatch: ObjectToIntersection<Omit<Merge<Types.Dispatch<A, S>, Types.DispatchRoot<AT>>, 'fetchApi'> & {
            fetchApi: <U extends Urls, N extends Methods<U>>(type: 'fetchApi', payload: ApiPayload<U, N>, options: {root: true}) => Promise<ApiResponse<U, N>>
        }>
    }
export declare type ComputedGetters<G extends GetterTree<S>, S> = Types.ComputedGetters<G, S>
export declare type Module<S> = Omit<Vuex.Module<S, State>, 'namespaced'> & {namespaced: true}
export declare type Store = Vuex.Store<State>

export function generateStore(security: Security): Store {
    return createStore({
        actions,
        modules: {security: generateSecurity(security)},
        mutations,
        state,
        strict: process.env.NODE_ENV !== 'production'
    })
}
