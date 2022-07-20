import Api from '../../../Api'
import {defineStore} from 'pinia'
import generateAttribute from './attribute'

export default defineStore('attributes-store', {
    actions: {
        dispose() {
            const items = [...this.items]
            this.$reset()
            for (const attribute of items)
                attribute.dispose()
            this.$dispose()
        },
        async fetch() {
            const response = await new Api().fetch('/api/attributes?pagination=false')
            if (response.status === 200)
                for (const attribute of response.content['hydra:member'])
                    this.items.push(generateAttribute(attribute))
        },
        update(attributes, family) {
            for (const attribute of this.items)
                attribute.update(attributes, family)
        }
    },
    state: () => ({items: []})
})
