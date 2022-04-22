import {CollectionRepository, EntityRepository, VatMessage} from '../../modules'
import store from '../../index'

export default class VatMessageRepository extends EntityRepository {
    use = VatMessage

    async create(body, vue) {
        this.loading(vue)
        const message = await this.fetch(vue, '/api/vat-messages', 'post', body)
        this.destroyAll(vue)
        this.save(message, vue)
        this.finish(vue)
    }

    async load(vue) {
        this.loading(vue)
        const response = await this.fetch(
            vue,
            '/api/vat-messages',
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
        await this.fetch(vue, '/api/vat-messages/{id}', 'delete', {id})
        this.destroy(id, vue)
        this.finish(vue)
    }

    tableItems(fields, vue) {
        const messages = this.where(message => message.vues.includes(vue))
        const coll = store.$repo(CollectionRepository).find(vue)
        if (coll !== null && coll.isSorted)
            messages.orderBy(coll.sort, coll.direction)
        return messages.get().map(message => message.tableItem(fields))
    }

    async update(body, vue) {
        this.loading(vue)
        const message = await this.fetch(vue, '/api/vat-messages/{id}', 'patch', body)
        this.save(message, vue)
        this.finish(vue)
    }
}
