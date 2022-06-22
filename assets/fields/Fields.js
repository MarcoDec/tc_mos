import Field from './Field'

export default class Fields {
    constructor(fields) {
        this.fields = []
        for (const field of fields)
            this.push(field)
    }

    * [Symbol.iterator]() {
        for (const field of this.fields)
            yield field
    }

    async fetch() {
        await Promise.all(this.fields.map(field => field.fetch()))
    }

    push(field) {
        this.fields.push(new Field(field))
    }
}
