import FieldOptions from './FieldOptions'

export default class Field {
    constructor(field) {
        this.create = field.create ?? true
        this.hideLabelValue = field.hideLabelValue ?? false
        this.label = field.label
        this.name = field.name
        this.options = field.options ? new FieldOptions(field.options) : null
        this.search = field.search ?? true
        this.sort = field.sort ?? true
        this.type = field.type ?? 'text'
        this.update = field.update ?? true
    }
}
