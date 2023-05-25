import api from '../../api'
import {defineStore} from 'pinia'
import useNode from './node'

export default function useTree(id) {
    return defineStore(id, {
        actions: {
            blur() {
                for (const node of this.nodes)
                    node.blur()
            },
            async create(data) {
                this.nodes.push(useNode(await api(this.url, 'POST', data), this))
            },
            dispose() {
                for (const node of this.nodes)
                    node.dispose()
                this.$dispose()
            },
            async fetchOne() {
                const response = await api(this.url)
                for (const node of response['hydra:member'])
                    this.nodes.push(useNode(node, this))
            },
            removeNode(removed) {
                this.nodes = this.nodes.filter(node => node.$id !== removed.$id)
            }
        },
        getters: {
            big: state => state.options.length > 30,
            find: state => iri => state.nodes.find(node => node['@id'] === iri) ?? null,
            findByParent: state => iri => state.nodes.filter(node => node.parent === iri),
            hasIcon() {
                return this.icon !== null
            },
            hasSelected() {
                return this.selected !== null
            },
            icon() {
                return this.selected?.filepath ?? null
            },
            options: state => state.nodes.map(node => node.option),
            roots: state => state.nodes.filter(node => node.root),
            selected: state => state.nodes.find(node => node.selected) ?? null,
            selectedKey() {
                return this.selected?.id ?? 'new'
            },
            title() {
                return this.selected?.fullName ?? 'Créer une nouvelle famille'
            },
            url: state => `/api/${state.id}`
        },
        state: () => ({id, nodes: [], valueProp: '@id'})
    })()
}
