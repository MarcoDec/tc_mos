import type {FieldState, RegistrableFieldState} from './Field'
import Field from './Field'
import {Getter} from '../../../module/Getter'
import Module from '../../../module/Module'
import type {ModuleState} from '../../../module/Module'
import {State} from '../../../module/State'

export interface RegistrableFormState extends ModuleState {
    readonly fields: RegistrableFieldState[]
    readonly id: string
}

export interface FormState extends RegistrableFormState {
    readonly fields: FieldState[]
}

export default class Form extends Module implements FormState {
    @State('name', Field, true) public readonly fields!: FieldState[]
    @Getter() public readonly fieldsName!: string[]
    @State() public readonly id!: string

    public static register(state: FormState): void {
        super.register(state, new Form())
    }
}
