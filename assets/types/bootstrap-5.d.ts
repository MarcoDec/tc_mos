export declare type BootstrapSize = 'lg' | 'md' | 'sm'

export declare type BootstrapVariant =
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

export declare type FormInput =
    'boolean'
    | 'date'
    | 'file'
    | 'number'
    | 'password'
    | 'radio'
    | 'search-boolean'
    | 'select'
    | 'text'
    | 'checkbox'


export declare type FormOption = {text: string, value: FormValue}
export declare type FormOptions = FormOption[]

export declare type FormField = {
    btn?: boolean
    id?: string
    label: string
    labelCols?: number
    name: string
    options?: FormOptions
    type?: FormInput
}

export declare type FormValue = boolean | number | string | null

export declare type FormValues = Record<string, FormValue>
