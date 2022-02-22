import type {State as Family} from './family'
import type {Violation} from '../../../../types/types'

export declare type State = {
    [key: string]: Family | Violation[] | string
    moduleName: string
    violations: Violation[]
}
