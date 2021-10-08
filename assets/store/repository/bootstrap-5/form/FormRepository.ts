import type {ComputedRef} from 'vue'
import type Field from '../../../entity/bootstrap-5/form/Field'
import Form from '../../../entity/bootstrap-5/form/Form'
import {computed} from 'vue'
import {registerFields} from './FieldRepository'
import {useStore} from 'vuex'

export function hasForm(form: string): ComputedRef<boolean> {
    return computed(() => useStore().hasModule(form))
}

export function registerForm(form: string, fields: Field[]): void {
    registerFields(form, fields)
    useStore().registerModule(form, new Form(form, fields))
}
