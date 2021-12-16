export type ItemField = {
    ajout:boolean
    code: string
    name: string
    type: number|null
    limite: string
    cadence: number|null
    prix: number|null
    temps: number|null
    deletable: boolean 
}
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

export type FormInput = 'boolean' | 'button' | 'date' | 'grpbutton' | 'number' | 'password' | 'select' | 'switch' | 'text' | 'time'

export type FormOption = {text: string, value: number | string}

export type FormField = {
    label: string
    name: string
    type?: FormInput
    options?: FormOption[]
    ajoutVisible: boolean
    updateVisible: boolean
    trie: boolean
}

export type FormValue = number | string

export type FormValues = Record<string, FormValue>
