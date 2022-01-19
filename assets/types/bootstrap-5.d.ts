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

export type FormInput = 'boolean' | 'date' | 'file' | 'number' | 'password' | 'radio' | 'search-boolean' | 'select' | 'text'

export type FormOption = {text: string, value: FormValue}
export type FormOptions = FormOption[]

export type FormField = {
    btn?: boolean
    id?: string
    label: string
    name: string
    options?: FormOptions
    type?: FormInput
}

export type FormValue = boolean | number | string | null
export type InputValue = Readonly<{name: string, value: FormValue}>

export type FormValues = Record<string, FormValue>
export type ReadFormValues = Readonly<Record<string, FormValue>>
