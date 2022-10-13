export default class FieldOptions {
    constructor(options) {
        if (typeof options.generate === 'function') {
            this.initialized = false
            this.options = null
            this.prepare = options
            this.storeMode = true
        } else {
            this.initialized = true
            this.options = options
            this.prepare = null
            this.storeMode = false
        }
    }

    dispose() {
        if (this.storeMode) {
            this.initialized = false
            this.options.dispose()
            this.options = null
            this.storeMode = true
        }
    }

    label(value) {
        return this.storeMode
            ? this.options.label(value)
            : this.options.find(option => option.value === value)?.text ?? null
    }

    async initialize() {
        if (this.initialized)
            return
        this.options = this.prepare.generate()
        await this.options.fetch()
        this.initialized = true
    }

    * [Symbol.iterator]() {
        yield* this.storeMode ? this.options.options : this.options
    }
}
