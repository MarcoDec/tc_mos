import {CollectionRepository, Color, EntityRepository} from '../../modules'
import store from '../..'

export default class ColorRepository extends EntityRepository {
    use = Color

    async create(body, vue) {
        this.loading(vue)
        const color = await this.fetch(vue, '/api/colors', 'post', body)
        this.destroyAll(vue)
        this.save(color, vue)
        this.finish(vue)
    }

    async load(vue) {
        this.loading(vue)
        const body = {}
        const collRepo = store.$repo(CollectionRepository)
        const coll = collRepo.find(vue)
        if (coll !== null)
            body.page = coll.page
        const response = await this.fetch(vue, '/api/colors', 'get', body)
        this.destroyAll(vue)
        this.save(response['hydra:member'])
        store.$repo(CollectionRepository).save(response, vue)
        this.finish(vue)
    }

    async remove(id, vue) {
        this.loading(vue)
        await this.fetch(vue, '/api/colors/{id}', 'delete', {id})
        this.destroy(id, vue)
        this.finish(vue)
    }

    tableItems(fields) {
        return this.all().map(color => color.tableItem(fields))
    }

    async update(body, vue) {
        this.loading(vue)
        const color = await this.fetch(vue, '/api/colors/{id}', 'patch', body)
        this.save(color, vue)
        this.finish(vue)
    }
}
