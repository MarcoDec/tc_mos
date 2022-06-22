import {defineStore} from 'pinia'
import generateField from './field'

export default function generateFields(form, fields) {
    return defineStore(form, {
        state: () => ({items: fields.map(field => generateField(form, field))})
    })
}
