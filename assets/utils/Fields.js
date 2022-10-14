import Field from './Field'
import {readonly} from 'vue'

export default class Fields {
    constructor(fields) {
        this.fields = fields.map(field => {
            const normalized = new Field(field)
            return normalized.options === null ? readonly(normalized) : normalized
        })
    }

    get action() {
        return this.create || this.search || this.update
    }

    get create() {
        return this.fields.some(field => field.create)
    }

    get search() {
        return this.fields.some(field => field.search)
    }

    get update() {
        return this.fields.some(field => field.update)
    }

    static generate(fields) {
        return new Fields(fields)
    }

    * [Symbol.iterator]() {
        yield* this.fields
    }
}
