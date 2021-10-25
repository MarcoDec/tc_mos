import FieldRepository from './bootstrap-5/FieldRepository'
import FormRepository from './bootstrap-5/FormRepository'
import ModuleRepository from './ModuleRepository'
import UserRepository from './security/UserRepository'
import {inject} from 'vue'

class RepositoryManager {
    public fields = new FieldRepository()
    public forms = new FormRepository()
    public users = new UserRepository()

    public async clear(vueComponent: string): Promise<void> {
        const cleared = []
        for (const repo of Object.values(this))
            if (repo instanceof ModuleRepository)
                cleared.push(repo.clear(vueComponent))
        await Promise.all(cleared)
    }
}

const manager = new RepositoryManager()
export default manager

export function useManager(): RepositoryManager {
    const injected = inject<RepositoryManager>('repositories')
    if (typeof injected !== 'undefined')
        return injected
    throw new Error('RepositoryManager not found.')
}
