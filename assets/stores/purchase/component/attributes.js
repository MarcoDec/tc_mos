import Api from '../../../Api'
import {defineStore} from 'pinia'
import generateAttribute from './attribute'

export default defineStore('attributes', {
    actions: {
        dispose() {
            this.$reset()
            for (const attribute of this.items)
                attribute.dispose()
            this.$dispose()
        },
        async fetch() {
            const response = await new Api().fetch('/api/attributes?pagination=false')
            if (response.status === 200)
                for (const attribute of response.content['hydra:member'])
                    this.items.push(generateAttribute(attribute))
        }
    },
    state: () => ({items: []})
})
