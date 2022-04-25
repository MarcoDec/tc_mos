import {CollectionRepository, EntityRepository, Incoterms} from '../../modules'
import store from '../../index'

export default class IncotermsRepository extends EntityRepository {
    use = Incoterms

    async create(body, vue) {
        this.loading(vue)
        const incoterms = await this.fetch(vue, '/api/incoterms', 'post', body)
        this.destroyAll(vue)
        this.save(incoterms, vue)
        this.finish(vue)
    }

    async load(vue) {
        this.loading(vue)
        const response = await this.fetch(
            vue,
            '/api/incoterms',
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
        await this.fetch(vue, '/api/incoterms/{id}', 'delete', {id})
        this.destroy(id, vue)
        this.finish(vue)
    }

    tableItems(fields, vue) {
        const incotermss = this.where(incoterms => incoterms.vues.includes(vue))
        const coll = store.$repo(CollectionRepository).find(vue)
        if (coll !== null && coll.isSorted)
            incotermss.orderBy(coll.sort, coll.direction)
        return incotermss.get().map(incoterms => incoterms.tableItem(fields))
    }

    async update(body, vue) {
        this.loading(vue)
        const incoterms = await this.fetch(vue, '/api/incoterms/{id}', 'patch', body)
        this.save(incoterms, vue)
        this.finish(vue)
    }
}
