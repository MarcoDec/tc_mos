import type Entity from '../entity/Entity'
import type {EntityState} from '../entity/Entity'
import ModuleRepository from './ModuleRepository'

export default abstract class EntityRepository<T extends Entity<S>, S extends EntityState> extends ModuleRepository<T> {
    public find(id: number): T | null {
        return super.find(id)
    }

    public abstract persist(vueComponent: string, state: S): T
}
