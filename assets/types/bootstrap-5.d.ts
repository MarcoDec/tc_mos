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
    options?: {text: string, value: FormValue}[]
    type?: FormInput
}

export type FormInput =
    'boolean'
    | 'button'
    | 'date'
    | 'file'
    | 'grpbutton'
    | 'number'
    | 'password'
    | 'select'
    | 'switch'
    | 'text'
    | 'time'

export type FormValue = boolean | number | string | null
export type InputValue = Readonly<{name: string, value: FormValue}>

export type FormValues = Record<string, FormValue>
export type ReadFormValues = Readonly<Record<string, FormValue>>
