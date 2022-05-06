import {Node, Repository} from '../modules'
import store from '..'

export default class NodeRepository extends Repository {
    use = Node

    get hasSelected() {
        return Boolean(this.selected)
    }

    get selected() {
        return this.where('selected', true).withAllRecursive().first()
    }

    get tree() {
        return this.withAllRecursive().get().filter(node => !node.hasParent)
    }

    static async create(body, repo, vue) {
        await store.$repo(repo).create(body, vue)
    }

    static async load(repo, vue) {
        await store.$repo(repo).load(vue)
    }

    static async remove(id, repo, vue) {
        await store.$repo(repo).remove(id, vue)
    }

    static async update(body, repo, vue) {
        await store.$repo(repo).update(body, vue)
    }

    destroyAll(vue, repo) {
        super.destroyAll(vue)
        store.$repo(repo).destroyAll(vue)
    }

    remove(entityId, repo, vue) {
        const id = `${repo.use.entity}-${entityId}`
        this.destroy(id, vue)
    }

    node(entity, relation, repo, vue) {
        const id = `${repo.use.entity}-${entity.id}`
        this.save({id, [relation]: {...entity, nodeId: id}}, vue)
    }

    select(id) {
        const tree = this.withAllRecursive().get()
        const selected = tree.find(node => node.id === id)
        for (const node of tree)
            if (node.id === selected.id)
                this.save({id: selected.id, opened: true, selected: true})
            else
                this.save({id: node.id, opened: selected.hasThisParent(node), selected: false})
    }

    unselect() {
        const tree = this.withAllRecursive().get()
        for (const node of tree)
            this.save({id: node.id, selected: false})
    }
}
