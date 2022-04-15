import {Entity} from '../../../modules'

export default class Employee extends Entity {
    static entity = 'employees'

    get isItAdmin() {
        return this.has('ROLE_IT_ADMIN')
    }

    get isManagementAdmin() {
        return this.has('ROLE_MANAGEMENT_ADMIN')
    }

    get isManagementReader() {
        return this.isManagementWriter || this.has('ROLE_MANAGEMENT_READER')
    }

    get isManagementWriter() {
        return this.isManagementAdmin || this.has('ROLE_MANAGEMENT_WRITER')
    }

    get isProjectAdmin() {
        return this.has('ROLE_PROJECT_ADMIN')
    }

    get isProjectReader() {
        return this.isProjectWriter || this.has('ROLE_PROJECT_READER')
    }

    get isProjectWriter() {
        return this.isProjectAdmin || this.has('ROLE_PROJECT_WRITER')
    }

    get isPurchaseAdmin() {
        return this.has('ROLE_PURCHASE_ADMIN')
    }

    get isPurchaseReader() {
        return this.isPurchaseWriter || this.has('ROLE_PURCHASE_READER')
    }

    get isPurchaseWriter() {
        return this.isPurchaseAdmin || this.has('ROLE_PURCHASE_WRITER')
    }

    static fields() {
        return {
            ...super.fields(),
            name: this.string(null),
            roles: this.attr([])
        }
    }

    has(role) {
        return this.roles?.includes(role) ?? false
    }
}
