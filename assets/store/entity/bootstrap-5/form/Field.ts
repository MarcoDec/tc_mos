import Module from '../../Module'
import Property from '../../Property'

export default class Field extends Module {
    public constructor(form: string, field: string) {
        super(`forms/${form}/${field}`, [new Property('name', 'string', field)])
    }
}
