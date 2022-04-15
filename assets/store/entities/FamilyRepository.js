import {AbstractFamily, EntityRepository, NodeRepository} from '../modules'
import store from '..'

export default class FamilyRepository extends EntityRepository {
    nodeRelation = null
    route = null
    use = AbstractFamily

    get options() {
        return [...this.withAll().get().map(family => family.option)].sort((a, b) => a.text.localeCompare(b.text))
    }

    async create(body, vue) {
        this.loading(vue)
        const family = await this.fetch(vue, this.route, 'post', body)
        this.node(family, vue)
        this.finish(vue)
    }

    async load(vue) {
        this.loading(vue)
        const response = await this.fetch(vue, this.route, 'get', {})
        for (const family of response['hydra:member'])
            this.node(family, vue)
        this.finish(vue)
    }

    async remove(id, vue) {
        this.loading(vue)
        await this.fetch(vue, `${this.route}/{id}`, 'delete', {id})
        store.$repo(NodeRepository).remove(id, this, vue)
        this.destroy(id, vue)
        this.finish(vue)
    }

    async update(body, vue) {
        this.loading(vue)
        const family = await this.fetch(vue, `${this.route}/{id}`, 'post', body)
        this.node(family, vue)
        this.finish(vue)
    }

    node(family, vue) {
        delete family.parent
        store.$repo(NodeRepository).node(family, this.nodeRelation, this, vue)
    }
}
