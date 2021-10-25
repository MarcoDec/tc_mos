import 'reflect-metadata'
import type {ActionContext, ActionTree, MutationTree} from 'vuex'
import {useNamespacedActions, useNamespacedMutations, useNamespacedState} from 'vuex-composition-helpers'
import store from '../store'

function Action(): ReturnType<typeof Reflect.metadata> {
    return Reflect.metadata('type', 'action')
}

export function Column(): ReturnType<typeof Reflect.metadata> {
    return Reflect.metadata('type', 'column')
}

type ModuleState = {
    namespace: string
    vueComponents: string[]
}

type State<T> = ModuleState & T

type ModuleAction<T> = ActionTree<State<T>, unknown>

type ModuleActionContext<T> = ActionContext<State<T>, unknown>

type ModuleMutation<T> = MutationTree<State<T>>

type ValuesOf<T> = T[keyof T]

export default abstract class Module<T> {
    public actions: ModuleAction<T>
    public mutations: ModuleMutation<T>
    @Column() public namespace!: string
    public readonly namespaced: boolean = true
    public state: State<T>
    @Column() public vueComponents!: string[]

    protected constructor(vueComponent: string, namespace: string, state: T) {
        this.actions = {}
        this.mutations = {}
        this.state = {...state, namespace, vueComponents: [vueComponent]}

        for (const key in this.state)
            this.mutations[key] = (storedState: State<T>, value: ValuesOf<typeof storedState>): void => {
                // eslint-disable-next-line @typescript-eslint/ban-ts-comment
                // @ts-ignore
                storedState[key as keyof State<T>] = value
            }
    }

    public get index(): string {
        const split = this.namespace.split('/')
        return split[split.length - 1]
    }

    private get columns(): string[] {
        const columns = []
        for (const property of Object.keys(this)) {
            const metadata = Reflect.getMetadata('type', this, property)
            if (typeof metadata === 'string' && metadata === 'column')
                columns.push(property)
        }
        return columns
    }

    private get methods(): string[] {
        const methods = []
        for (const method of Object.getOwnPropertyNames(Object.getPrototypeOf(Object.getPrototypeOf(this)))) {
            const metadata = Reflect.getMetadata('type', this, method)
            if (typeof metadata === 'string' && metadata === 'action')
                methods.push(method)
        }
        return methods
    }

    public defineActions(): this {
        for (const method of this.methods)
            // eslint-disable-next-line @typescript-eslint/ban-ts-comment
            // @ts-ignore
            this[method] = async (): Promise<void> => {
                await useNamespacedActions(store, this.state.namespace, [method])[method]
            }
        return this
    }

    public defineProperties(): this {
        for (const column of this.columns)
            Object.defineProperty(this, column, {
                get: () => useNamespacedState(store, this.state.namespace, [column])[column].value,
                set: value => {
                    useNamespacedMutations(store, this.state.namespace, [column])[column](value)
                }
            })
        return this
    }

    public mapActions(): this {
        const columns = this.columns
        for (const method of this.methods) {
            this.actions[method] = async (injectee: ModuleActionContext<T>): Promise<void> => {
                // eslint-disable-next-line @typescript-eslint/no-explicit-any
                const binder: any = {}
                for (const column of columns)
                    Object.defineProperty(binder, column, {
                        // eslint-disable-next-line @typescript-eslint/ban-ts-comment
                        // @ts-ignore
                        get: () => injectee.state[column],
                        set: value => {
                            injectee.commit(column, value)
                        }
                    })
                // eslint-disable-next-line @typescript-eslint/ban-ts-comment
                // @ts-ignore
                await this[method].bind(binder)()
            }
        }
        return this
    }

    public postPersist(): this {
        return this
            .mapActions()
            .register()
            .defineProperties()
            .defineActions()
    }

    public register(): this {
        const path: string[] = []
        for (const part of this.state.namespace.split('/')) {
            path.push(part)
            if (!store.hasModule(path))
                store.registerModule(path, path.join('/') === this.state.namespace ? this : {namespaced: true})
        }
        return this
    }

    @Action()
    public async remove(vueComponent: string): Promise<void> {
        this.vueComponents = this.vueComponents.filter(component => component !== vueComponent)
    }
}
