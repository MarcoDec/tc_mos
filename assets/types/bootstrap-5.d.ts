export type BootstrapSize = 'lg' | 'md' | 'sm'

export type ItemField = Record<string, FormValue>

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
export type FormValue = boolean | number | string | null
export type FormValues = Record<string, FormValue>
export type FormField = {
    label: string
    min: boolean
    name: string
    type?: FormInput
    options?: FormOption[]
    ajoutVisible: boolean
    updateVisible: boolean
    trie: boolean
}

