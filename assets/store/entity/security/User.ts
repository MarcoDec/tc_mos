import Entity from '../Entity'
import Property from '../Property'

export default class User extends Entity {
    public constructor(moduleName: string) {
        super(moduleName, [new Property('username')])
    }
}
