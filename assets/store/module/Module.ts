import {State} from './State'
import {registerModule} from './builder'

export interface ModuleState {
    readonly namespace: string[]
    readonly vueComponents: string[]
}

export default abstract class Module implements ModuleState {
    @State() public readonly namespace!: string[]
    @State() public readonly vueComponents!: string[]

    public static register(state: ModuleState, module: Module): void {
        registerModule(state, module)
    }
}
