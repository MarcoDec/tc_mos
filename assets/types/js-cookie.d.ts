declare module 'js-cookie' {
    type CookieOptions = {expires: number}
    type Cookies = {
        get: (name: string) => string | undefined
        remove: (name: string) => void
        set: (name: string, value: string, options: CookieOptions) => void
    }
    const cookies: Cookies
    export default cookies
}
