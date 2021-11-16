import Cookies from 'js-cookie'

export function get(name: string): string | undefined {
    return Cookies.get(name)
}

export function has(name: string): boolean {
    return typeof get(name) !== 'undefined'
}

export function remove(name: string): void {
    Cookies.remove(name)
}

export function set(name: string, value: string): string | undefined {
    return Cookies.set(name, value)
}
