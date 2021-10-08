import type Field from './Field'
import Module from '../../Module'
import Property from '../../Property'

export default class Form extends Module {
    public constructor(id: string, fields: Field[]) {
        super([
            new Property('fields', 'Field', fields.map(({moduleName}) => moduleName)),
            new Property('id', 'string', id)
        ])
    }
}
