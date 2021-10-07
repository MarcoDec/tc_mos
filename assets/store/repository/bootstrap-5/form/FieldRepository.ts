import type Field from '../../../entity/bootstrap-5/form/Field'
import {useStore} from 'vuex'

function registerField(form: string, field: Field): void {
    field.moduleName = `${form}[${field.moduleName}]`
    useStore().registerModule(field.moduleName, field)
}

export function registerFields(form: string, fields: Field[]): void {
    for (const field of fields)
        registerField(form, field)
}
