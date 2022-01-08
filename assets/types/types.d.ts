import type {A, O} from 'ts-toolbelt'

export declare type DeepReadonly<T extends object> = O.Readonly<T, A.Key, 'deep'>
