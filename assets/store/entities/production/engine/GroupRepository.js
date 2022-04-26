import {CollectionRepository, EntityRepository, Group} from '../../../modules'
import {ENGINE_GROUPS_API} from '../../../../utils'
import store from '../../../index'

export default class GroupRepository extends EntityRepository {
    use = Group

    static getUrl(body) {
        const url = ENGINE_GROUPS_API[body['@type']] ?? '/api/engine-groups'
        delete body['@type']
        return url
    }

    async create(body, vue) {
        this.loading(vue)
        if (!body['@type']) {
            this.error(vue, {
                violations: [{
                    message: 'Cette valeur ne doit pas être vide.',
                    propertyPath: '@type'
                }]
            }, 422)
            return
        }
        const group = await this.fetch(vue, GroupRepository.getUrl(body), 'post', body)
        this.destroyAll(vue)
        this.save(group, vue)
        this.finish(vue)
    }

    async load(vue) {
        this.loading(vue)
        const body = store.$repo(CollectionRepository).find(vue)?.body ?? {}
        const response = await this.fetch(vue, GroupRepository.getUrl(body), 'get', body)
        this.destroyAll(vue)
        this.save(response['hydra:member'], vue)
        store.$repo(CollectionRepository).save(response, vue)
        this.finish(vue)
    }

    async remove(id, vue) {
        this.loading(vue)
        await this.fetch(vue, '/api/engine-groups/{id}', 'delete', {id})
        this.destroy(id, vue)
        this.finish(vue)
    }

    tableItems(fields, vue) {
        const groups = this.where(group => group.vues.includes(vue))
        const coll = store.$repo(CollectionRepository).find(vue)
        if (coll !== null && coll.isSorted)
            groups.orderBy(coll.sort, coll.direction)
        return groups.get().map(group => group.tableItem(fields))
    }

    async update(body, vue) {
        this.loading(vue)
        const group = await this.fetch(vue, '/api/engine-groups/{id}', 'patch', body)
        this.save(group, vue)
        this.finish(vue)
    }
}
