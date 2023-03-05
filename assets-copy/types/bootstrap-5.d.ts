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

export type FormField = {
    label: string
    name: string
    type?: FormInput
}

export type FormInput = 'password' | 'text'

export type FormValue = number | string

export type FormValues = Record<string, FormValue>

export type Tabs = {
    name: string
    isActive: boolean
}

export type Tab = {
    active: {active: boolean}
    icon: string
    id: string
    labelledby: string
    target: string
    title: string
}