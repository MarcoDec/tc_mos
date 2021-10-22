import Cookies from 'js-cookie'
import EntityRepository from '../EntityRepository'
import type {Store} from 'vuex'
import User from '../../entity/security/User'
import type {UserState} from '../../entity/security/User'
import {request} from '../../../api'
import store from '../../store'
import {useState} from 'vuex-composition-helpers'

const COOKIE_NAME = 'VUESESSID'

type StoreState = {
    users: Record<number, unknown>[]
}

export default class UserRepository extends EntityRepository<User, UserState> {
    public get current(): User | null {
        const users = useState<StoreState>(store as Store<StoreState>, ['users']).users.value
        return typeof users !== 'undefined' && Object.keys(users).length > 0
            ? this.items.find(({isCurrent}) => isCurrent) || null
            : null
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
        const user = this.persist('vue', state, true)
        Cookies.set(COOKIE_NAME, state.username, {expires: 1 / 24})
        return user
    }

    // eslint-disable-next-line class-methods-use-this
    public async disconnect(): Promise<void> {
        await request({}, 'GET', '/api/logout')
        Cookies.remove(COOKIE_NAME)
    }

    public persist(vueComponent: string, state: UserState, isCurrent = false): User {
        state.isCurrent = isCurrent
        const item = new User(vueComponent, `users/${state.id}`, state)
        this.items.push(item)
        return this.postPersist(item)
    }
}
