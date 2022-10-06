import api from '../../api'
import {defineStore} from 'pinia'
import useNotification from './notification'

export default function useCategory(category, parent) {
    return defineStore(`category/${category}`, {
        actions: {
            dispose() {
                this.parent.removeCategory(this)
                for (const notification of this.notifications)
                    notification.dispose()
                this.$reset()
                this.$dispose()
            },
            push(notification) {
                this.notifications.push(useNotification(notification, this))
            },
            async reading() {
                const response = await api(`${this.url}read-all`, 'PATCH')
                for (const notification of response.content['hydra:member'])
                    this.byId[notification.id]?.reset(notification)
            },
            async remove() {
                await api(`${this.url}all`, 'DELETE')
                this.dispose()
            },
            removeNotification(removed) {
                this.notifications = this.notifications.filter(notification => notification.id !== removed.id)
                if (this.length === 0)
                    this.dispose()
            }
        },
        getters: {
            byId: state => {
                const notifications = {}
                for (const notification of state.notifications)
                    notifications[notification.id] = notification
                return notifications
            },
            length: state => state.notifications.length,
            read() {
                return this.readLength === 0
            },
            readLength: state => state.notifications.reduce((sum, item) => sum + (item.read ? 0 : 1), 0),
            url: state => `/api/notifications/${state.name}/`
        },
        state: () => ({name: category, notifications: [], parent})
    })()
}
