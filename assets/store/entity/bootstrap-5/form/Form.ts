import Module from '../../Module'
import Property from '../../Property'

export default class Form extends Module {
    public constructor(id: string) {
        super(`forms/${id}`, [new Property('id', 'string', id)])
    }
}
