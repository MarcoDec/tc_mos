import Module, {Column} from '../Module'
import type {InputType} from '../../../types/bootstrap-5'

export type FieldState = {
    form: string
    label: string
    name: string
    type: InputType
}

export default class Field extends Module<FieldState> {
    @Column() public form!: string
    @Column() public label!: string
    @Column() public name!: string
    @Column() public type!: InputType

    public constructor(vueComponent: string, form: string, label: string, name: string, namespace: string, type: InputType = 'text') {
        super(vueComponent, namespace, {form, label, name, type})
    }
}
