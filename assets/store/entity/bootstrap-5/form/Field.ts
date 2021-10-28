import type {InputType} from '../../../../types/bootstrap-5'
import Module from '../../../module/Module'
import type {ModuleState} from '../../../module/Module'
import {State} from '../../../module/State'

export interface FieldState extends ModuleState {
    readonly form: string
    readonly label: string
    readonly name: string
    readonly type?: InputType
}

export type RegistrableFieldState = Omit<FieldState, 'form' | 'namespace' | 'vueComponents'>

export default class Field extends Module implements FieldState {
    @State() public readonly form!: string
    @State() public readonly label!: string
    @State() public readonly name!: string
    @State() public readonly type!: InputType

    public static register(state: FieldState): void {
        super.register(state, new Field())
    }
}
