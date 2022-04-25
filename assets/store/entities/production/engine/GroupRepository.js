import {CollectionRepository, EntityRepository, Group} from '../../../modules'
import store from '../../../index'

export default class GroupRepository extends EntityRepository {
    use = Group

    async create(body, vue) {
        this.loading(vue)
        const group = await this.fetch(vue, '/api/engine-groups', 'post', body)
        this.destroyAll(vue)
        this.save(group, vue)
        this.finish(vue)
    }

    async load(vue) {
        this.loading(vue)
        const response = await this.fetch(
            vue,
            '/api/engine-groups',
            'get',
            store.$repo(CollectionRepository).find(vue)?.body ?? {}
        )
        this.destroyAll(vue)
        this.save(response['hydra:member'], vue)
        store.$repo(CollectionRepository).save(response, vue)
        this.finish(vue)
    }

    async remove(id, vue) {
        this.loading(vue)
        await this.fetch(vue, '/api/engine-groups/{id}', 'delete', {id})
        this.destroy(id, vue)
        this.finish(vue)
    }

    tableItems(fields, vue) {
        const groups = this.where(group => group.vues.includes(vue))
        const coll = store.$repo(CollectionRepository).find(vue)
        if (coll !== null && coll.isSorted)
            groups.orderBy(coll.sort, coll.direction)
        return groups.get().map(group => group.tableItem(fields))
    }

    async update(body, vue) {
        this.loading(vue)
        const group = await this.fetch(vue, '/api/engine-groups/{id}', 'patch', body)
        this.save(group, vue)
        this.finish(vue)
    }
}
