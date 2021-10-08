import type Field from '../../../entity/bootstrap-5/form/Field'
import {useStore} from 'vuex'

function registerField(field: Field): void {
    useStore().registerModule(field.moduleName, field)
}

export function registerFields(fields: Field[]): void {
    for (const field of fields)
        registerField(field)
}
