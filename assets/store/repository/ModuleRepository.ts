import type Module from '../entity/Module'
import {useStore} from 'vuex'

export default abstract class ModuleRepository<M extends Module> {
    private readonly modules: M[] = []

    protected constructor(protected readonly name: string) {
    }

    public find(module: string): M | null {
        if (this.has(module)) {
            const find = this.modules.find(m => m.module === module)
            if (find)
                return find
        }
        return null
    }

    public has(module: string): boolean {
        return useStore().hasModule(`${this.name}/${module}`)
    }

    public register(module: M): void {
        useStore().registerModule(module.module.split('/'), module)
        this.modules.push(module)
    }

    public registerAll(modules: M[]): void {
        for (const module of modules)
            this.register(module)
    }
}
