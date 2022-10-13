import Field from './Field'
import {readonly} from 'vue'

export default class Fields {
    constructor(fields) {
        this.fields = fields.map(field => {
            const normalized = new Field(field)
            return normalized.type === 'select' ? normalized : readonly(normalized)
        })
    }

    static generate(fields) {
        return new Fields(fields)
    }

    * [Symbol.iterator]() {
        yield* this.fields
    }
}
