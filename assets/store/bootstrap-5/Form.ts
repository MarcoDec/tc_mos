import {registerModule, store} from '../store'
import type {ComputedRef} from 'vue'
import type {Field} from './Field'
import {computed} from 'vue'
import {registerFields} from './Field'

const MODULE_NAME = 'forms'

class Form {
    public constructor(
        public readonly id: string,
        public readonly fields: readonly string[]
    ) {
    }
}

export function findFields(id: string): ComputedRef<string[]> {
    return computed((): string[] => store.state[`${MODULE_NAME}/${id}/fields`])
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
