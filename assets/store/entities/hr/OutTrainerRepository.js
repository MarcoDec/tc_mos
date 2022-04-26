import {CollectionRepository, EntityRepository, OutTrainer} from '../../modules'
import store from '../../index'

export default class OutTrainerRepository extends EntityRepository {
    use = OutTrainer

    async create(body, vue) {
        this.loading(vue)
        const trainer = await this.fetch(vue, '/api/out-trainers', 'post', body)
        this.destroyAll(vue)
        this.save(trainer, vue)
        this.finish(vue)
    }

    async load(vue) {
        this.loading(vue)
        const response = await this.fetch(
            vue,
            '/api/out-trainers',
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
        await this.fetch(vue, '/api/out-trainers/{id}', 'delete', {id})
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
            records.address.outTrainerId = records.id
            records.address.id = `${this.use.entity}-${records.id}`
        }
        return super.save(records, vue)
    }

    tableItems(fields, vue) {
        const trainers = this.where(trainer => trainer.vues.includes(vue))
        const coll = store.$repo(CollectionRepository).find(vue)
        if (coll !== null && coll.isSorted)
            trainers.orderBy(coll.sort, coll.direction)
        return trainers.withAll().get().map(trainer => trainer.tableItem(fields))
    }

    async update(body, vue) {
        this.loading(vue)
        const trainer = await this.fetch(vue, '/api/out-trainers/{id}', 'patch', body)
        this.save(trainer, vue)
        this.finish(vue)
    }
}
