import {CollectionRepository, EntityRepository, Group} from '../../../modules'
import {ENGINE_GROUPS_API} from '../../../../utils'
import store from '../../..'

export default class GroupRepository extends EntityRepository {
    use = Group
    url = '/api/engine-groups'

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
}
