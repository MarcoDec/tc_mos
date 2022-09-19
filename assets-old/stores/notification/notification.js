import Api from '../../Api'
import {defineStore} from 'pinia'

export default function generateNotification(notification) {
    return defineStore(`/api/notifications/${notification.id}`, {
        actions: {
            blur() {
                this.opened = false
                this.selected = false
            },
            dispose() {
                this.$reset()
                this.$dispose()
            },
            focus() {
                this.root.blur()
                this.selected = true
                this.open()
            },
            open() {
                this.opened = true
                this.parentStore?.open()
            },
            async remove() {
                await new Api().fetch(this.iri, 'DELETE')
                this.root.remove(this['@id'])
                this.dispose()
            }

        },
        state: () => ({...notification})
    })()
}
