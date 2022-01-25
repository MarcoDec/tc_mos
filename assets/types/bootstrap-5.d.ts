export type BootstrapSize = 'lg' | 'md' | 'sm'

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

export type FormInput = 'boolean' | 'date' | 'number' | 'password' | 'radio' | 'search-boolean' | 'select' | 'text'

export type FormValue = boolean | number | string | null

export type FormOption = {text: string, value: FormValue}

export type FormField = {
    btn?: boolean
    id?: string
    label: string
    name: string
    options?: FormOption[]
    type?: FormInput
}

export type FormValues = Record<string, FormValue>
