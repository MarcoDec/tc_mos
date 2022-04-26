import {Entity} from '../../../modules'

export default class Employee extends Entity {
    static entity = 'employees'

    get isItAdmin() {
        return this.has('ROLE_IT_ADMIN')
    }

    get isLogisticsAdmin() {
        return this.has('ROLE_LOGISTICS_ADMIN')
    }

    get isLogisticsReader() {
        return this.isLogisticsWriter || this.has('ROLE_LOGISTICS_READER')
    }

    get isLogisticsWriter() {
        return this.isLogisticsAdmin || this.has('ROLE_LOGISTICS_WRITER')
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

    get isProductionAdmin() {
        return this.has('ROLE_PRODUCTION_ADMIN')
    }

    get isProductionReader() {
        return this.isProductionWriter || this.has('ROLE_PRODUCTION_READER')
    }

    get isProductionWriter() {
        return this.isProductionAdmin || this.has('ROLE_PRODUCTION_WRITER')
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

    get isQualityAdmin() {
        return this.has('ROLE_QUALITY_ADMIN')
    }

    get isQualityReader() {
        return this.isQualityWriter || this.has('ROLE_QUALITY_READER')
    }

    get isQualityWriter() {
        return this.isQualityAdmin || this.has('ROLE_QUALITY_WRITER')
    }

    static fields() {
        return {
            ...super.fields(),
            name: this.string(null).nullable(),
            roles: this.attr([])
        }
    }

    has(role) {
        return this.roles?.includes(role) ?? false
    }
}
