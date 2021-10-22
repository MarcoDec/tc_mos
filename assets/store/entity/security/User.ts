import {Column} from '../Module'
import Entity from '../Entity'
import type {EntityState} from '../Entity'

export type UserState = EntityState & {
    isCurrent?: boolean
    name: string
    username: string
}

export default class User extends Entity<UserState> {
    @Column() public isCurrent!: boolean
    @Column() public name!: string
    @Column() public username!: string

    public constructor(vueComponent: string, namespace: string, state: UserState) {
        super(vueComponent, namespace, state)
    }
}
