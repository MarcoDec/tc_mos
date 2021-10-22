import Module, {Column} from './Module'

export type EntityState = {
    '@context': string
    '@id': string
    '@type': string
    id: number
}

export default abstract class Entity<T extends EntityState> extends Module<T> {
    @Column() public '@context'!: string
    @Column() public '@id'!: string
    @Column() public '@type'!: string
    @Column() public id!: number

    protected constructor(vueComponent: string, namespace: string, state: T) {
        super(vueComponent, namespace, state)
    }
}
