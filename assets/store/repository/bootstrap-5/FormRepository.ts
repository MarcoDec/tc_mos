import Form from '../../entity/bootstrap-5/Form'
import type {InputType} from '../../../types/bootstrap-5'
import ModuleRepository from '../ModuleRepository'
import {useManager} from '../RepositoryManager'

const MODULE_NAME = 'forms'

type Field = {label: string, name: string, type?: InputType}

export default class FormRepository extends ModuleRepository<Form> {
    public persist(id: string, fields: Field[]): Form {
        const namespace = `${MODULE_NAME}/${id}`
        const item = this.postPersist(new Form(id, namespace))
        this.items.push(item)
        for (const field of fields)
            useManager().fields
                .persist(id, field.label, field.name, namespace, field.type || 'text')
        return item
    }
}
