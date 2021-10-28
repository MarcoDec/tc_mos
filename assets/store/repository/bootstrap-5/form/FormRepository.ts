import {Action} from '../../../module/Action'
import FieldRepository from './FieldRepository'
import Form from '../../../entity/bootstrap-5/form/Form'
import Module from '../../../module/Module'
import type {ModuleState} from '../../../module/Module'
import type {RegistrableFormState} from '../../../entity/bootstrap-5/form/Form'
import {defineMembers} from '../../../module/builder'
import store from '../../../store'

export default class FormRepository extends Module {
    public static readonly baseNamespace: string = 'forms'

    public static find(id: string): Form | null {
        const namespace = [FormRepository.baseNamespace, id]
        if (!store.hasModule(namespace))
            return null
        const forms = new Form()
        defineMembers(forms, namespace.join('/'))
        return forms
    }

    public static register(state: ModuleState): void {
        super.register(state, new FormRepository())
    }

    @Action()
    public async persist(state: Omit<RegistrableFormState, 'namespace'>): Promise<void> {
        const namespace = [...this.namespace, state.id]
        const fieldsNamespace = [...namespace, FieldRepository.baseNamespace]
        const formState = {
            ...state,
            fields: state.fields.map(field => ({
                ...field,
                form: state.id,
                namespace: [...fieldsNamespace, field.name],
                type: typeof field.type !== 'undefined' ? field.type : 'text',
                vueComponents: state.vueComponents
            })),
            namespace
        }
        Form.register(formState)
        FieldRepository.registerAll(formState.fields, fieldsNamespace)
    }
}
