import type Module from '../entity/Module'

export default abstract class ModuleRepository<T extends Module<S>, S> {
    protected readonly items: T[] = []

    public async clear(vueComponent: string): Promise<void> {
        const removed = []
        for (const item of this.items)
            removed.push(item.remove(vueComponent))
        await Promise.all(removed)
    }

    public find(id: number | string): T | null {
        return this.items.find(({index}) => index === id) || null
    }

    public abstract persist(vueComponent: string, ...args: unknown[]): T
}
