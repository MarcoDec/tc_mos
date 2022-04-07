import CollectionRepository from '../collections/CollectionRepository'
import Color from './Color'
import {Repository} from '@vuex-orm/core'
import app from '../../app'

export default class ColorRepository extends Repository {
    use = Color

    static async fetchApi(url, method, body) {
        const response = await app.config.globalProperties.$store.dispatch('fetchApi', {body, method, url})
        return response
    }

    async create(body) {
        try {
            const color = await ColorRepository.fetchApi('/api/colors', 'post', body)
            this.fresh(color)
        } catch (e) {
            if (e instanceof Response && e.status === 422) {
                const violations = await e.json()
                return violations.violations
            }
            throw e
        }
        this.repo(CollectionRepository).unify(this.use.entity)
        return []
    }

    async delete(id) {
        await ColorRepository.fetchApi('/api/colors/{id}', 'delete', {id})
        this.destroy(id)
        this.repo(CollectionRepository)['delete'](this.use.entity)
    }

    async load(body = {}) {
        const colors = await ColorRepository.fetchApi('/api/colors', 'get', body)
        this.fresh(colors['hydra:member'])
        this.repo(CollectionRepository).create(this.use.entity, colors)
    }

    tableItems(fields) {
        return this.all().map(color => color.tableItem(fields))
    }

    async update(body) {
        try {
            const color = await ColorRepository.fetchApi('/api/colors/{id}', 'patch', body)
            this.save(color)
        } catch (e) {
            if (e instanceof Response && e.status === 422) {
                const violations = await e.json()
                return violations.violations
            }
            throw e
        }
        return []
    }
}
