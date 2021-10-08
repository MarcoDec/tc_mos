import {registerModule} from '../store'

export class Field {
    public form: string | null = null

    public constructor(public readonly name: string) {
    }
}

function registerField(form: string[], field: Field): void {
    registerModule(form.concat([field.name]), {state: field})
}

export function registerFields(form: string[], fields: Field[]): void {
    for (const field of fields)
        registerField(form, field)
}
