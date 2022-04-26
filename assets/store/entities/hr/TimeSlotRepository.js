import {CollectionRepository, EntityRepository, TimeSlot} from '../../modules'
import store from '../../index'

export default class TimeSlotRepository extends EntityRepository {
    use = TimeSlot

    async create(body, vue) {
        this.loading(vue)
        const slot = await this.fetch(vue, '/api/time-slots', 'post', body)
        this.destroyAll(vue)
        this.save(slot, vue)
        this.finish(vue)
    }

    async load(vue) {
        this.loading(vue)
        const response = await this.fetch(
            vue,
            '/api/time-slots',
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
        await this.fetch(vue, '/api/time-slots/{id}', 'delete', {id})
        this.destroy(id, vue)
        this.finish(vue)
    }

    tableItems(fields, vue) {
        const slots = this.where(slot => slot.vues.includes(vue))
        const coll = store.$repo(CollectionRepository).find(vue)
        if (coll !== null && coll.isSorted)
            slots.orderBy(coll.sort, coll.direction)
        return slots.get().map(slot => slot.tableItem(fields))
    }

    async update(body, vue) {
        this.loading(vue)
        const slot = await this.fetch(vue, '/api/time-slots/{id}', 'patch', body)
        this.save(slot, vue)
        this.finish(vue)
    }
}
