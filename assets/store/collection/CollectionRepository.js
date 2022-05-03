import {Collection, Repository} from '../modules'

export default class CollectionRepository extends Repository {
    use = Collection

    static extractPage(view, hydra) {
        if (typeof view !== 'object')
            return 1
        const subject = view[`hydra:${hydra}`]
        if (typeof subject === 'undefined')
            return 1
        const extracted = subject.match(/page=(\d+)/)
        return parseInt(extracted[1] ?? 1)
    }

    input(id, field, value) {
        const search = this.find(id)?.search ?? {}
        this.save({id, search: {...search, [field]: value}})
    }

    save(records, vue = null) {
        if (typeof records === 'object' && typeof records['hydra:totalItems'] === 'number') {
            const view = records['hydra:view']
            return super.save({
                first: CollectionRepository.extractPage(view, 'first'),
                id: vue,
                last: CollectionRepository.extractPage(view, 'last'),
                next: CollectionRepository.extractPage(view, 'next'),
                prev: CollectionRepository.extractPage(view, 'prev'),
                total: records['hydra:totalItems']
            }, vue)
        }
        return super.save(records, vue)
    }

    resetSearch(coll) {
        this.save({id: coll.id, search: {}})
    }

    setPage(page, id) {
        this.save({id, page})
    }

    sort(coll, field) {
        const clone = {...coll}
        if (clone.sort === field.name)
            clone.asc = !clone.asc
        else {
            clone.asc = false
            clone.sort = field.name
            clone.sortName = field.sortName ?? field.name
        }
        this.save(clone)
    }
}
