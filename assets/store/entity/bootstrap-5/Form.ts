import Module, {Column} from '../Module'
import store from '../../store'
import {useNamespacedState} from 'vuex-composition-helpers'

type FormState = {id: string}

type FormFields = {fields: string[]}

export default class Form extends Module<FormState> {
    @Column() public id!: string

    public constructor(id: string, namespace: string) {
        super(namespace, {id})
    }

    public get fields(): string[] {
        return Object.keys(useNamespacedState<FormFields>(store, this.namespace, ['fields']).fields.value)
    }
}
