import type {State as Item} from './item'
import type {Violation} from '../../types/types'

export declare type State = {
    [key: string]: Item | Violation[] | string
    moduleName: string
    url: string
    violations: Violation[]
}
