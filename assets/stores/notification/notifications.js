import Api from '../../Api'
import {defineStore} from 'pinia'
import generateNotification from './notification'

export default defineStore('notifications', {
    actions: {
        async fetch() {
            const response = await new Api().fetch('/api/notifications')
            if (response.status === 200)
                for (const notification of response.content['hydra:member'])
                    this.items.push(generateNotification(notification, this))
        },

        async read(notification) {
            const response = await new Api().fetch(`/api/notifications/${notification}`, 'PATCH', notification)
            if (response.status === 422)
                throw response.content.violations
            this.$state = {...response.content}
        },
        async readCategory(category) {
            const response = await new Api().fetch('/api/notifications/{category}/read-all', 'PATCH', category)
            if (response.status === 422)
                throw response.content.violations
            this.$state = {...response.content}
        },
        async remove(notification) {
            const response = await new Api().fetch(`/api/notifications/${notification}`, 'DELETE', notification)
            if (response.status === 422)
                throw response.content.violations
            this.$state = {...response.content}
        },

        async removeCategory(category) {
            const response = await new Api().fetch(`/api/notifications/${category}/all`, 'DELETE', category)
            if (response.status === 422)
                throw response.content.violations
            this.$state = {...response.content}
        }
    },
    getters: {
        findByCategories: state => {
            const categories = {}
            for (const notification of state.items) {
                if (!categories[notification.category])
                    categories[notification.category] = []
                categories[notification.category].push(notification)
            }
            return Object.entries(categories)
        },
        isEmpty: state => state.items.length === 0,
        length: state => state.items.filter(item => !item.read).length
    },
    state: () => ({items: []})
})
