import ModuleRepository from '../../ModuleRepository'

class FieldRepository extends ModuleRepository {
    public constructor() {
        super('fields')
    }
}

export default new FieldRepository()
