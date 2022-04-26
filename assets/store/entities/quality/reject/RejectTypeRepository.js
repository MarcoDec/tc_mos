import {CollectionRepository, EntityRepository, RejectType} from '../../../modules'
import store from '../../../index'

export default class RejectTypeRepository extends EntityRepository {
    use = RejectType

    async create(body, vue) {
        this.loading(vue)
        const type = await this.fetch(vue, '/api/reject-types', 'post', body)
        this.destroyAll(vue)
        this.save(type, vue)
        this.finish(vue)
    }

    async load(vue) {
        this.loading(vue)
        const response = await this.fetch(
            vue,
            '/api/reject-types',
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
        await this.fetch(vue, '/api/reject-types/{id}', 'delete', {id})
        this.destroy(id, vue)
        this.finish(vue)
    }

    tableItems(fields, vue) {
        const types = this.where(type => type.vues.includes(vue))
        const coll = store.$repo(CollectionRepository).find(vue)
        if (coll !== null && coll.isSorted)
            types.orderBy(coll.sort, coll.direction)
        return types.get().map(type => type.tableItem(fields))
    }

    async update(body, vue) {
        this.loading(vue)
        const type = await this.fetch(vue, '/api/reject-types/{id}', 'patch', body)
        this.save(type, vue)
        this.finish(vue)
    }
}
