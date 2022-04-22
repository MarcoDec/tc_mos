import {Carrier, CollectionRepository, EntityRepository} from '../../modules'
import store from '../../index'

export default class CarrierRepository extends EntityRepository {
    use = Carrier

    async create(body, vue) {
        this.loading(vue)
        const carrier = await this.fetch(vue, '/api/carriers', 'post', body)
        this.destroyAll(vue)
        this.save(carrier, vue)
        this.finish(vue)
    }

    async load(vue) {
        this.loading(vue)
        const response = await this.fetch(
            vue,
            '/api/carriers',
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
        await this.fetch(vue, '/api/carriers/{id}', 'delete', {id})
        this.destroy(id, vue)
        this.finish(vue)
    }

    save(records, vue = null) {
        if (Array.isArray(records)) {
            const saved = []
            for (const record of records)
                saved.push(this.save(record, vue))
            return saved
        }
        if (typeof records.address.id !== 'string') {
            records.address.carrierId = records.id
            records.address.id = `${this.use.entity}-${records.id}`
        }
        return super.save(records, vue)
    }

    tableItems(fields, vue) {
        const carriers = this.where(carrier => carrier.vues.includes(vue))
        const coll = store.$repo(CollectionRepository).find(vue)
        if (coll !== null && coll.isSorted)
            carriers.orderBy(coll.sort, coll.direction)
        return carriers.withAll().get().map(carrier => carrier.tableItem(fields))
    }

    async update(body, vue) {
        this.loading(vue)
        const carrier = await this.fetch(vue, '/api/carriers/{id}', 'patch', body)
        this.save(carrier, vue)
        this.finish(vue)
    }
}
