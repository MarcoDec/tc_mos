import Cookies from 'js-cookie'

function setExpirableCookie(name, value) {
    const expires = new Date()
    expires.setHours(expires.getHours() + 1)
    Cookies.set(name, value, {expires})
}

export function get(name) {
    return Cookies.get(name)
}

export function has() {
    return typeof get('id') !== 'undefined' && typeof get('token') !== 'undefined'
}

export function remove() {
    Cookies.remove('id')
    Cookies.remove('token')
}

export function set(id, token) {
    setExpirableCookie('id', id.toString())
    setExpirableCookie('token', token)
}

export function renew() {
    const id = get('id')
    const token = get('token')
    if (typeof id !== 'undefined' && typeof token !== 'undefined') {
        const intId = parseInt(id)
        if (!isNaN(intId))
            set(intId, token)
    }
}
