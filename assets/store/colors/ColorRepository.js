import Color from './Color'
import {Repository} from '@vuex-orm/core'
import app from '../../app'

export default class ColorRepository extends Repository {
    use = Color

    async create(body) {
        try {
            const color = await app.config.globalProperties.$store.dispatch('fetchApi', {
                body,
                method: 'post',
                url: '/api/colors'
            })
            this.fresh(color)
        } catch (e) {
            if (e instanceof Response && e.status === 422) {
                const violations = await e.json()
                return violations.violations
            }
            throw e
        }
        return []
    }

    async load(body = {}) {
        const colors = await app.config.globalProperties.$store.dispatch('fetchApi', {
            body,
            method: 'get',
            url: '/api/colors'
        })
        this.fresh(colors['hydra:member'])
    }

    tableItems(fields) {
        return this.all().map(color => color.tableItem(fields))
    }
}
