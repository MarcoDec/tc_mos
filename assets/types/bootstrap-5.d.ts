export type ItemField = {
    code: string
    name: string
    type: number
    limite: string
    cadence: number
    prix: number
    temps: number
}

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

export type FormInput = 'boolean' | 'button' | 'date' | 'grpbutton' | 'number' | 'password' | 'select' | 'switch' | 'text' | 'time'
export type FormOption = {text: string, value: number | string}

export type FormField = {
    label: string
    name: string
    type?: FormInput
    options?: FormOption[]
}

