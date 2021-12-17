export type ItemField = {
    ajout: boolean
    code: string
    name: string
    type: number | null
    limite: string
    cadence: number | null
    prix: number | null
    temps: number | null
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

// export type FormInput = 'boolean' | 'button' | 'date' | 'grpbutton' | 'number' | 'password' | 'select' | 'switch' | 'text' | 'time'

export type FormInput = 'boolean' | 'password' | 'radio' | 'search-boolean' | 'select' | 'text'

export type FormOption = {text: string, value: number | string}

export type FormField = {
    btn?: boolean
    label: string
    name: string
    type?: FormInput
    options?: FormOption[]
    ajoutVisible: boolean
    updateVisible: boolean
    trie: boolean
}

export type FormValue = boolean | number | string | null

export type FormValues = Record<string, FormValue>
