import {Color, EntityRepository} from '../../modules'

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
        const response = await this.fetch(vue, '/api/colors', 'get', {})
        this.save(response['hydra:member'])
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
