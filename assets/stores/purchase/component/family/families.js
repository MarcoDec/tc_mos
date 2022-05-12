import {defineStore} from 'pinia'
import fetchApi from '../../../../api'
import generateFamily from './family'

export default defineStore('component-family', {
    actions: {
        dispose() {
            for (const family of this.families)
                family.$dispose()
            this.$dispose()
        },
        async fetch() {
            const response = await fetchApi('/api/component-families')
            if (response.status === 200)
                for (const family of response.content['hydra:member'])
                    this.families.push(generateFamily(family, this))
        }
    },
    getters: {find: state => iri => state.families.find(family => family['@id'] === iri)},
    state: () => ({families: []})
})
