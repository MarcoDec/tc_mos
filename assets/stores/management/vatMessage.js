import {defineStore} from 'pinia'
import fetchApi from '../../api'

export default function generateMessage(message, root) {
    return defineStore(`vat-message/${message.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            },
            async update(data) {
                const response = await fetchApi(`/api/vat-messages/${this.id}`, 'PATCH', data)
                if (response.status === 422)
                    throw response.content.violations
                this.$state = {root: this.root, ...response.content}
            }
        },
        state: () => ({root, ...message})
    })()
}
