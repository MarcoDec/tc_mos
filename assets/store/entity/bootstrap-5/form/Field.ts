import Module from '../../Module'
import Property from '../../Property'

export default class Field extends Module {
    // public readonly name?: string
    // public readonly type?: string

    public constructor(form: string, field: string) {
        super(`${form}[${field}]`, [
            new Property('name', 'string', field),
            new Property('type', 'string', 'string')
        ])
    }
}
