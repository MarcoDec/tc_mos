import Api from '../../Api'
import {defineStore} from 'pinia'

export default function generateColor(color, root) {
    return defineStore(`color/${color.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            },
            async update(fields, data) {
                const response = await new Api(fields).fetch(`/api/colors/${this.id}`, 'PATCH', data)
                if (response.status === 422)
                    throw response.content.violations
                this.$state = {root: this.root, ...response.content}
            }
        },
        state: () => ({root, ...color})
    })()
}
