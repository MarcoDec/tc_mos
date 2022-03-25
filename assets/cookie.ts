import Cookies from 'js-cookie'

function setExpirableCookie(name: string, value: string): void {
    const expires = new Date()
    expires.setHours(expires.getHours() + 1)
    Cookies.set(name, value, {expires})
}

export function get(name: string): string | undefined {
    return Cookies.get(name)
}

export function has(): boolean {
    return typeof get('id') !== 'undefined' && typeof get('token') !== 'undefined'
}

export function remove(): void {
    Cookies.remove('id')
    Cookies.remove('token')
}

export function set(id: number, token: string): void {
    setExpirableCookie('id', id.toString())
    setExpirableCookie('token', token)
}

export function renew(): void {
    const id = get('id')
    const token = get('token')
    if (typeof id !== 'undefined' && typeof token !== 'undefined') {
        const intId = parseInt(id)
        if (!isNaN(intId))
            set(intId, token)
    }
}
