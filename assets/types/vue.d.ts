import type {GetterTree} from 'vuex'
import type {SetupContext} from 'vue'

export declare type FunContext = Omit<SetupContext, 'expose'>
export declare type FunProps = Record<string, unknown>
export declare type ComputedGetters<G extends GetterTree<S, R>, S, R> = {[key in keyof G]: ReturnType<G[key]>}
