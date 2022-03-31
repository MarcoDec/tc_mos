import 'vuex'
import {ActionTree, Getter, ModuleTree, MutationTree} from 'vuex'

declare module 'vuex' {
    export type Getter<S, R = any> = (state: S, getters: any, rootState: R, rootGetters: any) => any;

    export interface GetterTree<S, R = any> {
        [key: string]: Getter<S, R>;
    }

    export interface Module<S, R = any> {
        namespaced?: boolean;
        state?: S | (() => S);
        getters?: GetterTree<S, R>;
        actions?: ActionTree<S, R>;
        mutations?: MutationTree<S>;
        modules?: ModuleTree<R>;
    }
}
