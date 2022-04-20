import {Collection, Repository} from '../modules'

export default class CollectionRepository extends Repository {
    use = Collection

    static extractPage(view, hydra) {
        const subject = view[`hydra:${hydra}`]
        if (typeof subject === 'undefined')
            return 1
        const extracted = subject.match(/page=(\d+)/)
        return parseInt(extracted[1] ?? 1)
    }

    save(records, vue = null) {
        if (typeof records === 'object' && typeof records['hydra:view'] === 'object') {
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

    setPage(page, id) {
        this.save({id, page})
    }
}
