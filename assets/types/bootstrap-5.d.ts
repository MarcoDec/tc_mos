export type BootstrapVariant =
    'body'
    | 'danger'
    | 'dark'
    | 'info'
    | 'light'
    | 'primary'
    | 'secondary'
    | 'success'
    | 'transparent'
    | 'warning'
    | 'white'

export type FormInput = 'password' | 'text'

export type FormField = {
    label: string
    name: string
    type?: FormInput
}

export type UserResponse = {
    name: string,
    embRoles: {
        roles: string[]
    },
    username: string,
    id: string,
    token: string

}
