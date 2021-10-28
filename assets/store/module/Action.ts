/* eslint-disable @typescript-eslint/ban-ts-comment,@typescript-eslint/explicit-function-return-type */
import {Metadata, VUEX_TYPE_ACTION, getMetadata, vuexMetadata} from './Metadata'
import type {ModuleActionContext, StoreState} from '../store'
import type {ActionTree} from 'vuex'
import type Module from './Module'
import type {ModuleState} from './Module'
import {getState} from './State'
import store from '../store'
import {useNamespacedActions} from 'vuex-composition-helpers'

type ModuleActionTree<S> = ActionTree<S, StoreState>

export function Action(): ReturnType<typeof vuexMetadata> {
    return vuexMetadata(new Metadata(VUEX_TYPE_ACTION))
}

function getActions(module: Module): string[] {
    const members = new Set<string>()
    let current = module
    do {
        for (const property of Object.getOwnPropertyNames(current))
            members.add(property)
    } while ((current = Object.getPrototypeOf(current)) !== null)

    const filter = (member: string): boolean => {
        // @ts-ignore
        if (typeof module[member] === 'function') {
            const metadata = getMetadata(module, member)
            if (metadata instanceof Metadata && metadata.type === VUEX_TYPE_ACTION)
                return true
        }
        return false
    }

    return Array.from(members).filter(filter)
}

function binder<S>(injectee: ModuleActionContext<S>, module: Module) {
    const binderBuilder = {}
    for (const property in getState(module))
        Object.defineProperty(binderBuilder, property, {
            enumerable: true,
            // @ts-ignore
            get: () => injectee.state[property]
        })
    return binderBuilder
}

export function buildAction<S extends ModuleState>(module: Module): ModuleActionTree<S> {
    const actions: ModuleActionTree<S> = {}
    for (const action of getActions(module)) {
        // @ts-ignore
        const method = module[action]
        actions[action] = async (injectee: ModuleActionContext<S>, payload) => {
            await method.bind(binder(injectee, module))(payload)
        }
    }
    return actions
}

export function defineActions(module: Module, namespace: string): void {
    for (const method of getActions(module))
        // @ts-ignore
        module[method] = async payload => {
            const result = await useNamespacedActions(store, namespace, [method])[method](payload)
            return result
        }
}
