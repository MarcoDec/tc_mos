import Entity from '../Entity'
import Property from '../Property'

export default class User extends Entity {
    public constructor(id: number) {
        super(`user[${id}]`, [new Property('username')])
    }
}
