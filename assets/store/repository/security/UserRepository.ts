import Cookies from 'js-cookie'
import EntityRepository from '../EntityRepository'
import User from '../../entity/security/User'
import type {UserState} from '../../entity/security/User'
import {request} from '../../../api'

const COOKIE_NAME = 'VUESESSID'

export default class UserRepository extends EntityRepository<User, UserState> {
    public get current(): User | null {
        return this.items.find(({isCurrent}) => isCurrent) || null
    }

    public async hasCurrent(): Promise<boolean> {
        if (typeof Cookies.get(COOKIE_NAME) === 'undefined')
            return false
        if (this.current !== null)
            return true
        const response = await request({}, 'GET', '/api/users/current') as UserState | null
        if (response !== null) {
            this.connect(response)
            return true
        }
        return false
    }

    public connect(state: UserState): User {
        const user = this.persist(state)
        user.isCurrent = true
        Cookies.set(COOKIE_NAME, state.username, {expires: 1 / 24})
        return user
    }

    public persist(state: UserState): User {
        const item = new User(`users/${state.id}`, state)
        this.items.push(item)
        return this.postPersist(item)
    }
}
