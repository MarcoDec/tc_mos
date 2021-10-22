import Field from '../../entity/bootstrap-5/Field'
import type {InputType} from '../../../types/bootstrap-5'
import ModuleRepository from '../ModuleRepository'

export default class FieldRepository extends ModuleRepository<Field> {
    public persist(vueComponent: string, form: string, label: string, name: string, namespace: string, type: InputType = 'text'): Field {
        const item = new Field(vueComponent, form, label, name, `${namespace}/fields/${name}`, type)
        this.items.push(item)
        return this.postPersist(item)
    }
}
