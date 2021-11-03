import type {
    CommitOptions,
    DispatchOptions,
    Module,
    Store as VuexStore
} from 'vuex'
import type {Actions} from './actions'
import type {Getters} from './getters'
import type {Mutations} from './mutations'
import type {RootState} from '../index'
import type {State} from './state'
import {actions} from './actions'
import {getters} from './getters'
import {mutations} from './mutations'
import {state} from './state'


export {State}

export type UserStore<S = State> = Omit<VuexStore<S>, 'commit' | 'dispatch' | 'getters'>
& {
    commit: <K extends keyof Mutations, P extends Parameters<Mutations[K]>[1]>(
        key: K,
        payload: P,
        options?: CommitOptions
    ) => ReturnType<Mutations[K]>;
} & {
    dispatch: <K extends keyof Actions>(
        key: K,
        payload: Parameters<Actions[K]>[1],
        options?: DispatchOptions
    ) => ReturnType<Actions[K]>;
}
& {
    getters: {
        [K in keyof Getters]: ReturnType<Getters[K]>
    };
}

export const store: Module<State, RootState> = {
    actions,
    getters,
    mutations,
    namespaced: true,
    state

}


