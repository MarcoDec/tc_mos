import type {components} from '../../../../../types/openapi'

export declare type Family = components['schemas']['ComponentFamily.jsonld-ComponentFamily-read']
export declare type TreeItemAction = {opened: boolean, selected: boolean}
export declare type State = Family & TreeItemAction & {moduleName: string, parentModuleName: string}
