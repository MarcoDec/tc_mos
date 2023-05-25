import api from '../../api'
import {defineStore} from 'pinia'
import useCategory from './category'

export default defineStore('notifications', {
    actions: {
        dispose() {
            for (const category of this.categories)
                category.dispose()
            this.$dispose()
        },
        async fetchOne() {
            const response = await api('/api/notifications')
            const categories = {}
            for (const notification of response['hydra:member']) {
                if (!categories[notification.category])
                    categories[notification.category] = useCategory(notification.category, this)
                categories[notification.category].push(notification)
            }
            this.categories = Object.values(categories)
        },
        removeCategory(removed) {
            this.categories = this.categories.filter(category => category.name !== removed.name)
        }
    },
    getters: {
        isEmpty() {
            return this.length === 0
        },
        length() {
            return this.categories.reduce((sum, category) => sum + category.length, 0)
        },
        readLength() {
            return this.categories.reduce((sum, category) => sum + category.readLength, 0)
        },
        variant() {
            return this.readLength > 0 ? 'danger' : 'secondary'
        }
    },
    state: () => ({categories: []})
})
