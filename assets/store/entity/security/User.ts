import Entity from '../Entity'
import type {EntityState} from '../Entity'

export type UserState = EntityState & {
    name: string
    username: string
}

export default class User extends Entity<UserState> {
    public isCurrent: boolean = false

    public constructor(namespace: string, state: UserState) {
        super(namespace, state)
    }
}
