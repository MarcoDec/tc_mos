import {CollectionRepository, Entity, FiniteStateMachineRepository, Repository} from '../modules'
import fetchApi from '../../api'
import {get} from 'lodash'
import store from '../index'

export default class EntityRepository extends Repository {
    use = Entity
    url = '/api/entities'

    get idUrl() {
        return `${this.url}/{id}`
    }

    get stateMachineRepo() {
        return this.repo(FiniteStateMachineRepository)
    }

    static sorter(first, second, coll) {
        const a = get(first, coll.sort)
        const b = get(second, coll.sort)
        switch (typeof a) {
        case 'number':
            return a - b
        case 'string':
            return a.localeCompare(b)
        default:
            return typeof first.id === 'number' ? first.id - second.id : first.id.localCompare(second.id)
        }
    }

    async create(body, vue) {
        this.loading(vue)
        const entity = await this.fetch(vue, this.url, 'post', body)
        this.destroyAll(vue)
        this.save(entity, vue)
        this.finish(vue)
    }

    error(vue, error, status) {
        this.stateMachineRepo.error(vue, error, status)
    }

    finish(vue) {
        this.stateMachineRepo.reset(vue)
    }

    async fetch(vue, url, method, body) {
        try {
            const response = await fetchApi(url, method, body)
            return response
        } catch (e) {
            if (e instanceof Response) {
                const error = await e.json()
                this.error(vue, error, e.status)
            } else
                this.error(vue, 'Une erreur s\'est produite.', 500)
            throw e
        }
    }

    async load(vue) {
        this.loading(vue)
        const response = await this.fetch(
            vue,
            this.url,
            'get',
            store.$repo(CollectionRepository).find(vue)?.body ?? {}
        )
        this.destroyAll(vue)
        this.save(response['hydra:member'], vue)
        store.$repo(CollectionRepository).save(response, vue)
        this.finish(vue)
    }

    loading(vue) {
        this.stateMachineRepo.load(vue)
    }

    async remove(id, vue) {
        this.loading(vue)
        await this.fetch(vue, this.idUrl, 'delete', {id})
        this.destroy(id, vue)
        this.finish(vue)
    }

    tableItems(fields, vue) {
        const entities = this.where(entity => entity.vues.includes(vue))
            .withAll()
            .get()
            .map(entity => entity.tableItem(fields))
        const coll = store.$repo(CollectionRepository).find(vue)
        if (coll !== null && coll.isSorted)
            entities.sort((first, second) =>
                (coll.direction === 'asc'
                    ? EntityRepository.sorter(first, second, coll)
                    : EntityRepository.sorter(second, first, coll)))
        return entities
    }

    async update(body, vue) {
        this.loading(vue)
        const entity = await this.fetch(vue, this.idUrl, 'patch', body)
        this.save(entity, vue)
        this.finish(vue)
    }
}
