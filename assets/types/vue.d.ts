/* eslint-disable @typescript-eslint/no-explicit-any */
import type {GetterTree} from 'vuex'
import type {SetupContext} from 'vue'

export declare type FunContext = Omit<SetupContext, 'expose'>
export declare type FunProps = Record<string, unknown>
export declare type ComputedGetters<G extends GetterTree<S, R>, S, R> = {[key in keyof G]: ReturnType<G[key]>}

export declare type Commit<T extends object, S> = {
    [K in keyof T]: Payload<T, K, S> extends never
        ? (type: K) => void
        : (type: K, payload: Payload<T, K, S>) => void
}
export declare type Dispatch<T extends object, S> = {
    [K in keyof T]: Payload<T, K, S> extends never
        ? (type: K) => Promise<void>
        : (type: K, payload: Payload<T, K, S>) => Promise<void>
}
export declare type DispatchRoot<T extends object> = {
    [K in keyof T]: (type: K, payload: Payload<T, K, any> extends never ? null : Payload<T, K, any>, options: {root: true}) => Promise<void>
}

declare type Payload<T extends object, K extends keyof T, S> = T[K] extends (...args: any) => any
    ? (Parameters<T[K]> extends [S, infer P] ? P : never)
    : never
