import {EntityRepository, Notification} from '../../../modules'

export default class NotificationRepository extends EntityRepository {
    use = Notification
    url = '/api/notifications'

    get findByCategories() {
        const categories = {}
        for (const notification of this.all()) {
            if (!categories[notification.category])
                categories[notification.category] = []
            categories[notification.category].push(notification)
        }
        return Object.entries(categories)
    }

    get length() {
        return this.where('read', false).get().length
    }

    async load(vue) {
        this.loading(vue)
        const response = await this.fetch(vue, this.url, 'get')
        this.destroyAll(vue)
        this.save(response['hydra:member'], vue)
        this.finish(vue)
    }

    async read(notification, vue = 'login') {
        this.loading(vue)
        const read = await this.fetch(vue, this.idUrl, 'patch', {id: notification.id})
        this.save(read)
        this.finish(vue)
    }

    async readCategory(category, vue = 'login') {
        this.loading(vue)
        const response = await this.fetch(vue, `${this.url}/{category}/read-all`, 'patch', {category})
        this.save(response['hydra:member'])
        this.finish(vue)
    }

    async remove(id, vue = 'login') {
        await super.remove(id, vue)
    }

    async removeCategory(category, vue = 'login') {
        this.loading(vue)
        await this.fetch(vue, `${this.url}/{category}/all`, 'delete', {category})
        this.query().where('category', category)['delete']()
        this.finish(vue)
    }
}
