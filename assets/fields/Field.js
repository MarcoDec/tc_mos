import generateOptions from '../stores/options'

export default class Field {
    static types = ['boolean', 'color', 'file', 'number', 'password', 'select', 'text', 'time']

    constructor(options) {
        if (typeof options !== 'object' || options === null || Array.isArray(options))
            throw new Error('field must be not null object')
        if (typeof options.label !== 'string')
            throw new Error('field.label must be defined and a string')
        this.label = options.label
        if (typeof options.name !== 'string')
            throw new Error('field.name must be defined and a string')
        this.name = options.name
        this._options = {options: []}
        this.optionsInitialized = false
        this.optionsStore = null
        if (typeof options.type === 'undefined')
            this.type = 'text'
        else {
            if (typeof options.type !== 'string' || !Field.types.includes(options.type))
                throw new Error(`field.type must be on of [${Field.types.join(', ')}]`)
            this.type = options.type
            if (this.type === 'select') {
                if (typeof options.options !== 'string')
                    throw new Error('field.options must be defined and a string')
                this.optionsStore = options.options
            }
        }
    }

    get options() {
        return this._options
    }

    set options(options) {
        this._options = options
        this.optionsInitialized = true
    }

    async fetch() {
        if (!this.optionsInitialized)
            this.options = generateOptions(this.optionsStore)
        await this.options?.fetch()
    }
}
