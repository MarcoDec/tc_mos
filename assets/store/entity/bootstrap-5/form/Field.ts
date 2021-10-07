import Module from '../../Module'
import Property from '../../Property'

export default class Field extends Module {
    public moduleName: string

    public constructor(field: string) {
        super([new Property('name', 'string', field)])
        this.moduleName = field
    }
}
