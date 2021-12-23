import Cookies from 'js-cookie'

const ONE_HOUR = 1

function setExpirableCookie(name: string, value: string): void {
    const expires = new Date()
    expires.setHours(expires.getHours() + ONE_HOUR)
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
