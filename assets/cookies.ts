import {connect, hasUser as hasStoredUser} from './store/store'
import Cookies from 'js-cookie'
import type {UserResponse} from './store/security/User'
import {initUser} from './store/security/User'

const COOKIE_NAME = 'VUESESSID'

export function createCookie(user: UserResponse): void {
    connect(user)
    Cookies.set(COOKIE_NAME, user.username, {expires: 1 / 24})
}

function hasCookie(): boolean {
    return typeof Cookies.get(COOKIE_NAME) !== 'undefined'
}

export async function hasUser(): Promise<boolean> {
    if (hasCookie()) {
        if (hasStoredUser().value)
            return true

        const user = await initUser()
        if (user !== null) {
            connect(user)
            return true
        }
    }
    return false
}
