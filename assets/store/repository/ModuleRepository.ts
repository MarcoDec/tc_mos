import type Module from '../entity/Module'
import {useStore} from 'vuex'

export default abstract class ModuleRepository {
    private readonly modules: Module[] = []

    protected constructor(protected readonly name: string) {
    }

    public has(module: string): boolean {
        return useStore().hasModule(`${this.name}/${module}`)
    }

    public register(module: Module): void {
        useStore().registerModule(module.module.split('/'), module)
        this.modules.push(module)
    }

    public registerAll(modules: Module[]): void {
        for (const module of modules)
            this.register(module)
    }
}
