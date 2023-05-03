import type {Field} from './Field'
import type {Ref} from 'vue'
import {registerFields} from './Field'
import {registerModule} from '../store'
import {useNamespacedState} from 'vuex-composition-helpers'

const MODULE_NAME = 'forms'

class Form {
    public constructor(
        public readonly id: string,
        public readonly fields: readonly string[]
    ) {
    }
}

export function findFields(id: string): Ref<readonly string[]> {
    return useNamespacedState<Form>(`${MODULE_NAME}/${id}`, ['fields']).fields
}

export function registerForm(id: string, fields: Field[]): void {
    const form = [MODULE_NAME, id]
    const formModule = form.join('/')
    const fieldModules: string[] = []
    for (const field of fields) {
        field.form = id
        fieldModules.push(`${formModule}/${field.name}`)
    }
    registerModule(form, {state: new Form(id, fieldModules)})
    registerFields(form, fields)
}
