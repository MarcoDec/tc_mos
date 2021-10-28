import 'reflect-metadata'
import type Module from './Module'

export const VUEX_TYPE_ACTION = 'action'
export const VUEX_TYPE_GETTER = 'getter'
export const VUEX_TYPE_STATE = 'state'
export const VUEX_TYPE = 'type'

type VuexMember = typeof VUEX_TYPE_ACTION | typeof VUEX_TYPE_GETTER | typeof VUEX_TYPE_STATE

export class Metadata {
    public constructor(public readonly type: VuexMember) {
    }
}

export function getMetadata(module: Module, property: string): Metadata | null {
    return Reflect.getMetadata(VUEX_TYPE, module, property)
}

export function vuexMetadata(value: Metadata): ReturnType<typeof Reflect.metadata> {
    return Reflect.metadata(VUEX_TYPE, value)
}
