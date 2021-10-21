import type Module from '../entity/Module'

export default abstract class ModuleRepository<T extends Module> {
    protected readonly items: T[] = []

    public find(id: number | string): T | null {
        return this.items.find(({index}) => index === id) || null
    }

    public abstract persist(...args: unknown[]): T

    // eslint-disable-next-line class-methods-use-this
    protected postPersist(item: T): T {
        return item.register().defineProperties()
    }
}
