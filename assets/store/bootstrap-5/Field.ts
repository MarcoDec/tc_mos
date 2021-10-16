import type {ExtractGetterTypes, RefTypes} from 'vuex-composition-helpers/dist/types/util'
import {useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'
import {registerModule} from '../store'

export class Field {
    public form: string = ''

    public constructor(
        public readonly label: string,
        public readonly name: string,
        public readonly type: 'password' | 'text' = 'text'
    ) {
    }
}

export type FieldGetter = {
    id: string
}

export function findField(moduleName: string): RefTypes<Field> {
    return useNamespacedState<Field>(moduleName, ['form', 'label', 'name', 'type'])
}

export function findFieldGetter(moduleName: string): ExtractGetterTypes<FieldGetter> {
    return useNamespacedGetters<FieldGetter>(moduleName, ['id'])
}

function registerField(form: string[], field: Field): void {
    registerModule(form.concat([field.name]), {
        getters: {
            id: (state: Field): string => `${state.form}[${state.name}]`
        },
        state: field
    })
}

export function registerFields(form: string[], fields: Field[]): void {
    for (const field of fields)
        registerField(form, field)
}
