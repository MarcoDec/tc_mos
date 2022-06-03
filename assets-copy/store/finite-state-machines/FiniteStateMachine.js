import {Model} from '../modules'

export default class FiniteStateMachine extends Model {
    static entity = 'finite-state-machines'

    get isOnError() {
        return Boolean(this.error) || this.violations.length > 0
    }

    static fields() {
        return {
            ...super.fields(),
            error: this.string(null).nullable(),
            id: this.string(null),
            loading: this['boolean'](false),
            status: this.number(200),
            violations: this.attr([])
        }
    }

    findViolation(field) {
        return this.violations.find(violation => violation.propertyPath === field.name) ?? null
    }
}
