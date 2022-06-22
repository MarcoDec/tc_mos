import Fields from './Fields'
import TableField from './TableField'

export default class TableFields extends Fields {
    push(field) {
        this.fields.push(new TableField(field))
    }
}
