import {CollectionRepository, EntityRepository, Unit} from '../../modules'
import store from '../..'

export default class UnitRepository extends EntityRepository {
    use = Unit

    get options() {
        return [...this.withAll().get().map(unit => unit.option)].sort((a, b) => a.text.localeCompare(b.text))
    }

    async create(body, vue) {
        this.loading(vue)
        const unit = await this.fetch(vue, '/api/units', 'post', body)
        this.destroyAll(vue)
        this.save(unit, vue)
        this.finish(vue)
    }

    async load(vue) {
        this.loading(vue)
        const response = await this.fetch(
            vue,
            '/api/units',
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
        await this.fetch(vue, '/api/units/{id}', 'delete', {id})
        this.destroy(id, vue)
        this.finish(vue)
    }

    tableItems(fields, vue) {
        const units = this.where(unit => unit.vues.includes(vue))
        const coll = store.$repo(CollectionRepository).find(vue)
        if (coll !== null && coll.isSorted)
            units.orderBy(coll.sort, coll.direction)
        return units.withAll().get().map(unit => unit.tableItem(fields))
    }

    async update(body, vue) {
        this.loading(vue)
        const unit = await this.fetch(vue, '/api/units/{id}', 'patch', body)
        this.save(unit, vue)
        this.finish(vue)
    }
}
