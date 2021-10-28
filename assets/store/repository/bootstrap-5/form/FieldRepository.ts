import type {FieldState} from '../../../entity/bootstrap-5/form/Field'
import Module from '../../../module/Module'
import store from '../../../store'

export default class FieldRepository extends Module {
    public static readonly baseNamespace: string = 'fields'

    public static register(state: FieldState): void {
        super.register(state, new FieldRepository())
    }

    public static registerAll(fields: FieldState[], namespace: string[]): void {
        store.registerModule(namespace, {namespaced: true})
        for (const field of fields)
            FieldRepository.register(field)
    }
}
