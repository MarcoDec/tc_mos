import type Field from '../../../entity/bootstrap-5/form/Field'
import ModuleRepository from '../../ModuleRepository'

class FieldRepository extends ModuleRepository<Field> {
    public constructor() {
        super('fields')
    }
}

export default new FieldRepository()
