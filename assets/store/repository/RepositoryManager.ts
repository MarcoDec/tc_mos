import FieldRepository from './bootstrap-5/FieldRepository'
import FormRepository from './bootstrap-5/FormRepository'
import ModuleRepository from './ModuleRepository'
import UserRepository from './security/UserRepository'
import {inject} from 'vue'

class RepositoryManager {
    public fields = new FieldRepository()
    public forms = new FormRepository()
    public users = new UserRepository()

    public clear(vueComponent: string): void {
        Object.values(this).forEach(repo => {
            if (repo instanceof ModuleRepository)
                repo.clear(vueComponent)
        })
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
