import Entity from '../Entity'
import Property from '../Property'

export default class User extends Entity {
    public constructor() {
        super([new Property('username')])
    }
}
