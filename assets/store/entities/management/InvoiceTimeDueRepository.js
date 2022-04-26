import {CollectionRepository, EntityRepository, InvoiceTimeDue} from '../../modules'
import store from '../../index'

export default class InvoiceTimeDueRepository extends EntityRepository {
    use = InvoiceTimeDue

    async create(body, vue) {
        this.loading(vue)
        const time = await this.fetch(vue, '/api/invoice-time-dues', 'post', body)
        this.destroyAll(vue)
        this.save(time, vue)
        this.finish(vue)
    }

    async load(vue) {
        this.loading(vue)
        const response = await this.fetch(
            vue,
            '/api/invoice-time-dues',
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
        await this.fetch(vue, '/api/invoice-time-dues/{id}', 'delete', {id})
        this.destroy(id, vue)
        this.finish(vue)
    }

    tableItems(fields, vue) {
        const times = this.where(time => time.vues.includes(vue))
        const coll = store.$repo(CollectionRepository).find(vue)
        if (coll !== null && coll.isSorted)
            times.orderBy(coll.sort, coll.direction)
        return times.get().map(time => time.tableItem(fields))
    }

    async update(body, vue) {
        this.loading(vue)
        const time = await this.fetch(vue, '/api/invoice-time-dues/{id}', 'patch', body)
        this.save(time, vue)
        this.finish(vue)
    }
}
