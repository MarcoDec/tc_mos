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

export type Tabs = {
    name: string
    isActive: boolean
}
export type FormInput = 'boolean' | 'date' | 'number' | 'password' | 'radio' | 'search-boolean' | 'select' | 'text' | 'button'

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

export type Tab = {
    active: {active: boolean}
    icon: string
    id: string
    labelledby: string
    target: string
    title: string
}
