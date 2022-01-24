import type {A, O} from 'ts-toolbelt'
import type {components} from './openapi'

export declare type DeepReadonly<T extends object> = O.Readonly<T, A.Key, 'deep'>

export declare type Merge<O1, O2> =
    O1 extends object
        ? O2 extends object ? O.Merge<O1, O2, 'deep'> : O2
        // eslint-disable-next-line @typescript-eslint/ban-types
        : O2 extends object ? O2 : {}

export type Violation = components['schemas']['Violation']
export type Violations = components['schemas']['Violations']
