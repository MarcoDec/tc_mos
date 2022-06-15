import * as Cookies from '../../../../cookie'
import {Employee, EntityRepository} from '../../../modules'
import fetchApi from '../../../../api'

export default class EmployeeRepository extends EntityRepository {
    use = Employee

    get hasUser() {
        return this.all().length === 1
    }

    get user() {
        return this.all()[0]
    }

    async login(vue, body) {
        this.loading(vue)
        const user = await this.fetch(vue, '/api/login', 'post', body)
        Cookies.set(user.id, user.token)
        this.save(user, vue)
        this.finish(vue)
    }

    async fetchUser(vue) {
        if (Cookies.has())
            try {
                const user = await fetchApi('/api/employees/{id}', 'get', {id: Cookies.get('id')})
                this.save(user, vue)
                return
                // eslint-disable-next-line no-empty
            } catch (e) {
            }
        Cookies.remove()
    }

    async logout(vue) {
        await this.fetch(vue, '/api/logout', 'post', {})
        Cookies.remove()
        this.destroy(this.user.id, vue)
    }
}
