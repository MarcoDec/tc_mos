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
            dispose() {
                for (const node of this.nodes)
                    node.dispose()
                this.$dispose()
            },
            async fetch() {
                const response = await api(this.url)
                for (const node of response['hydra:member'])
                    this.nodes.push(useNode(node, this))
            },
            removeNode(removed) {
                this.nodes = this.nodes.filter(node => node.$id !== removed.$id)
            }
        },
        getters: {
            find: state => iri => state.nodes.find(node => node['@id'] === iri),
            findByParent: state => iri => state.nodes.filter(node => node.parent === iri),
            roots: state => state.nodes.filter(node => node.root),
            url: state => `/api/${state.id}`
        },
        state: () => ({id, nodes: []})
    })()
}
