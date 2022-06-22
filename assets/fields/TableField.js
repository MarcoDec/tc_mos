import Field from './Field'

export default class TableField extends Field {
    constructor(options) {
        super(options)
        this.sort = Boolean(options.sort)
        this.update = Boolean(options.update)
        if (typeof options.sortName !== 'undefined' && (typeof options.sortName !== 'string' || options.sortName.length === 0))
            throw new Error('field.sortName must be a non empty string')
        this.sortName = options.sortName ?? options.name
    }
}
