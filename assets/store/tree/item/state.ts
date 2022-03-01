import type {Violation} from '../../../types/types'
import type {components} from '../../../types/openapi'

export declare type Item =
    components['schemas']['ComponentFamily.jsonld-ComponentFamily-read']
    & components['schemas']['ProductFamily.jsonld-ProductFamily-read']
export declare type TreeItemAction = {opened: boolean, selected: boolean}
export declare type State =
    Item
    & TreeItemAction
    & {baseUrl: string, moduleName: string, parentModuleName: string, url: string, violations: Violation[]}
