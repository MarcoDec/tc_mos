import type {O} from 'ts-toolbelt'
import type {components} from './openapi'

export declare type Merge<O1, O2> =
    O1 extends object
        // eslint-disable-next-line @typescript-eslint/no-unnecessary-type-arguments
        ? O2 extends object ? O.Merge<O1, O2, 'deep'> : O2
        // eslint-disable-next-line @typescript-eslint/ban-types
        : O2 extends object ? O2 : {}

export declare type ObjectToIntersection<T extends object> = UnionToIntersection<T[keyof T]>
// eslint-disable-next-line @typescript-eslint/no-explicit-any
declare type UnionToIntersection<T> = (T extends any ? (x: T) => any : never) extends (x: infer R) => any ? R : never

export declare type Violation = components['schemas']['Violation']
export declare type Violations = components['schemas']['Violations']
