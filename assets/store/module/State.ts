/* eslint-disable @typescript-eslint/ban-ts-comment,@typescript-eslint/ban-types,@typescript-eslint/no-unsafe-argument,@typescript-eslint/explicit-function-return-type */
import {Metadata, VUEX_TYPE_STATE, getMetadata, vuexMetadata} from './Metadata'
import {useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'
import type Module from './Module'
import type {ModuleState} from './Module'
import {defineMembers} from './builder'
import {startcase} from '@stdlib/string'
import store from '../store'

class StateMetadata extends Metadata {
    public constructor(
        public readonly relation: string | null = null,
        public readonly target: Function | null = null,
        public readonly many: boolean = false
    ) {
        super(VUEX_TYPE_STATE)
    }

    public relationId(property: string): string | null {
        return this.relation !== null ? `${property}${startcase(this.relation)}` : null
    }
}

type StateProperties = Record<string, StateMetadata>

export function State(relation: string | null = null, target: Function | null = null, many = false): ReturnType<typeof vuexMetadata> {
    return vuexMetadata(new StateMetadata(relation, target, many))
}

export function getState(module: Module): StateProperties {
    const properties: StateProperties = {}
    for (const property in module) {
        const metadata = getMetadata(module, property)
        if (metadata instanceof StateMetadata && metadata.type === VUEX_TYPE_STATE)
            properties[property] = metadata
    }
    return properties
}

export function buildState<S extends ModuleState>(module: Module, state: S): S {
    const cloned = {}
    for (const [property, metadata] of Object.entries(getState(module)))
        if (typeof metadata.relation !== 'string')
            // @ts-ignore
            cloned[property] = state[property]
    // @ts-ignore
    return cloned
}

// @ts-ignore
function getRelation(id, metadata: StateMetadata, namespace: string) {
    console.debug({id, metadata, namespace})
    if (metadata.target !== null) {
        const itemNamespace = `${namespace}/${id}`
        console.debug(itemNamespace)
        const item = Object.create(metadata.target.prototype)
        defineMembers(item, itemNamespace)
        console.debug(itemNamespace, item)
        return item
    }
    return null
}

export function defineStates(module: Module, namespace: string): void {
    for (const [property, metadata] of Object.entries(getState(module)))
        if (typeof metadata.relation === 'string') {
            const relation = metadata.relationId(property)
            // @ts-ignore
            const id = useNamespacedGetters(store, namespace, [relation])[relation].value
            const propertyNamespace = `${namespace}/${property}`
            if (metadata.many)
                // @ts-ignore
                Object.defineProperty(module, property, {
                    enumerable: true,
                    // @ts-ignore
                    get: () => id.map(itemId => getRelation(itemId, metadata, propertyNamespace))
                })
            else
                Object.defineProperty(module, property, {
                    enumerable: true,
                    get: () => getRelation(id, metadata, propertyNamespace)
                })
        } else
            Object.defineProperty(module, property, {
                enumerable: true,
                get: () => useNamespacedState(store, namespace, [property])[property].value
            })
}
