import Collection from './Collection'
import {Repository} from '@vuex-orm/core'

export default class CollectionRepository extends Repository {
    use = Collection

    static extractPage(view, hydra) {
        const subject = view[`hydra:${hydra}`]
        if (typeof subject === 'undefined')
            return 1
        const extracted = subject.match(/page=(\d+)/)
        return parseInt(extracted[1] ?? 1)
    }

    create(entity, response) {
        const collection = {name: entity, total: response['hydra:totalItems']}
        const view = response['hydra:view']
        if (typeof view !== 'undefined') {
            collection.first = Math.min(CollectionRepository.extractPage(view, 'first'))
            collection.last = CollectionRepository.extractPage(view, 'last')
            collection.next = CollectionRepository.extractPage(view, 'next')
            collection.prev = CollectionRepository.extractPage(view, 'previous')
        }
        this.save(collection)
    }

    delete(entity) {
        const collection = this.find(entity)
        this.save({...collection, total: collection.total - 1})
    }

    unify(entity) {
        this.save({name: entity, total: 1})
    }
}
