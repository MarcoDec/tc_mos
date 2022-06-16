import Api from '../../Api'
import {defineStore} from 'pinia'
import generateNotification from './notification'

export default function generateNotifications(iriType) {
    return defineStore(iriType, {
        actions: {
           
            async fetch() {
                const response = await new Api().fetch(this.iri)
                if (response.status === 200)
                    for (const family of response.content['hydra:member'])
                        this.items.push(generateNotification(this.iriType, family, this))
            }
        },
        getters: {
          
        },
        state: () => ({iriType, items: []})
    })()
}
