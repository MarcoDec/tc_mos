import Entity from '../Entity'
import type {EntityResponse} from '../Entity'

export type UserResponse = EntityResponse & {
    name: string
    username: string
}

export default class User extends Entity {
    public readonly name: string
    public readonly username: string

    public constructor(user: UserResponse) {
        super(user)
        this.name = user.name
        this.username = user.username
    }
}
