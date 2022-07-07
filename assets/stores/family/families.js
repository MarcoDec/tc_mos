import Api from '../../Api'
import {defineStore} from 'pinia'
import generateFamily from './family'

export default function generateFamilies(iriType) {
    return defineStore(iriType, {
        actions: {
            blur() {
                for (const family of this.items)
                    family.blur()
            },
            async create(fields, data) {
                const response = await new Api(fields).fetch(this.iri, 'POST', data, false)
                if (response.status === 422)
                    throw response.content.violations
                this.items.push(generateFamily(this.iriType, response.content, this))
            },
            dispose() {
                const items = [...this.items]
                this.$reset()
                for (const family of items)
                    family.dispose()
                this.$dispose()
            },
            async fetch() {
                const response = await new Api().fetch(this.iri)
                if (response.status === 200)
                    for (const family of response.content['hydra:member'])
                        this.items.push(generateFamily(this.iriType, family, this))
            },
            remove(removed) {
                this.items = this.items.filter(family => family['@id'] !== removed)
            }
        },
        getters: {
            find: state => iri => state.items.find(family => family['@id'] === iri),
            findByParent: state => iri => {
                const children = []
                for (const family of state.items)
                    if (family.parent === iri)
                        children.push(family)
                return children
            },
            hasSelected() {
                return Boolean(this.selected)
            },
            iri: state => `/api/${state.iriType}`,
            options: state => state.items.map(family => family.option).sort((a, b) => a.text.localeCompare(b.text)),
            roots: state => state.items.filter(family => family.isRoot).sort((a, b) => a.name.localeCompare(b.name)),
            selected: state => state.items.find(family => family.selected)
        },
        state: () => ({iriType, items: []})
    })()
}
