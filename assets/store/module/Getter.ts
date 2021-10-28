/* eslint-disable @typescript-eslint/ban-ts-comment,@typescript-eslint/explicit-function-return-type,@typescript-eslint/no-unsafe-argument */
import {Metadata, VUEX_TYPE_GETTER, getMetadata, vuexMetadata} from './Metadata'
import type {GetterTree} from 'vuex'
import type Module from './Module'
import type {ModuleState} from './Module'
import type {StoreState} from '../store'
import {getState} from './State'
import store from '../store'
import {useNamespacedGetters} from 'vuex-composition-helpers'

type ModuleGetterTree<S> = GetterTree<S, StoreState>

type GetterProperties = Record<string, Metadata>

export function Getter(): ReturnType<typeof vuexMetadata> {
    return vuexMetadata(new Metadata(VUEX_TYPE_GETTER))
}

function getGetters(module: Module): GetterProperties {
    const getters: GetterProperties = {}
    for (const property in module) {
        const metadata = getMetadata(module, property)
        if (metadata instanceof Metadata && metadata.type === VUEX_TYPE_GETTER)
            getters[property] = metadata
    }
    return getters
}

export function buildGetters<S extends ModuleState>(module: Module): ModuleGetterTree<S> {
    const getters: ModuleGetterTree<S> = {}
    for (const [property, metadata] of Object.entries(getState(module)))
        if (typeof metadata.relation === 'string') {
            if (metadata.many)
                // @ts-ignore
                getters[metadata.relationId(property)] = state => Object.keys(state[property])
            else
                // @ts-ignore
                getters[metadata.relationId(property)] = state => state[property]
        }
    return getters
}

export function defineGetters(module: Module, namespace: string): void {
    for (const property in getGetters(module))
        Object.defineProperty(module, property, {
            enumerable: true,
            get: () => useNamespacedGetters(store, namespace, [property])[property].value
        })
}
