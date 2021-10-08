import type Field from '../../../entity/bootstrap-5/form/Field'
import Form from '../../../entity/bootstrap-5/form/Form'
import ModuleRepository from '../../ModuleRepository'
import fieldRepository from './FieldRepository'
import {useStore} from 'vuex'

class FormRepository extends ModuleRepository {
    public constructor() {
        super('forms')
    }

    public createForm(form: string, fields: Field[]): void {
        this.createFormModule()
        this.register(new Form(form))
        fieldRepository.registerAll(fields)
    }

    private createFormModule(): void {
        const store = useStore()
        if (!store.hasModule(this.name))
            store.registerModule(this.name, {})
    }
}

export default new FormRepository()
