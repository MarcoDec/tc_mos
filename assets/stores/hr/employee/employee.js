import api from '../../../api'
import {defineStore} from 'pinia'

export const baseHierarchie = [
    'ROLE_LEVEL_DIRECTOR',
    'ROLE_LEVEL_MANAGER',
    'ROLE_LEVEL_ANIMATOR',
    'ROLE_LEVEL_OPERATOR'
]
export const baseAccounting = [
    'ROLE_ACCOUNTING_ADMIN',
    'ROLE_ACCOUNTING_WRITER',
    'ROLE_ACCOUNTING_READER'
]
export const baseProduction = [
    'ROLE_PRODUCTION_ADMIN',
    'ROLE_PRODUCTION_WRITER',
    'ROLE_PRODUCTION_READER'
]
export const baseQuality = [
    'ROLE_QUALITY_ADMIN',
    'ROLE_QUALITY_WRITER',
    'ROLE_QUALITY_READER'
]
export const basePurchase = [
    'ROLE_PURCHASE_ADMIN',
    'ROLE_PURCHASE_WRITER',
    'ROLE_PURCHASE_READER'
]
export const baseSelling = [
    'ROLE_SELLING_ADMIN',
    'ROLE_SELLING_WRITER',
    'ROLE_SELLING_READER'
]
export const baseLogistics = [
    'ROLE_LOGISTICS_ADMIN',
    'ROLE_LOGISTICS_WRITER',
    'ROLE_LOGISTICS_READER'
]
export const baseMaintenance = [
    'ROLE_MAINTENANCE_ADMIN',
    'ROLE_MAINTENANCE_WRITER',
    'ROLE_MAINTENANCE_READER'
]
export const baseIt = [
    'ROLE_IT_ADMIN'
]
export const baseHr = [
    'ROLE_HR_ADMIN',
    'ROLE_HR_WRITER',
    'ROLE_HR_READER'
]
export const baseManagement = [
    'ROLE_MANAGEMENT_ADMIN',
    'ROLE_MANAGEMENT_WRITER',
    'ROLE_MANAGEMENT_READER'
]
export const baseProject = [
    'ROLE_PROJECT_READER',
    'ROLE_PROJECT_WRITER',
    'ROLE_PROJECT_ADMIN'
]
export default function generateEmployee(employee) {
    return defineStore(`employees/${employee.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            },
            async update(data) {
                const response = await api(`/api/employees/${employee.id}/main`, 'PATCH', data)
                this.$state = {...response}
            },
            async updateContactEmp(data, id) {
                const response = await api(`/api/employee-contacts/${id}`, 'PATCH', data)
                this.$state = {...response}
            },
            async updateHr(data) {
                const response = await api(`/api/employees/${employee.id}/hr`, 'PATCH', data)
                this.$state = {...response}
            },
            async updateIt(data) {
                const response = await api(`/api/employees/${employee.id}/it`, 'PATCH', data)
                this.$state = {...response}
            },
            async updateProd(data) {
                const response = await api(`/api/employees/${employee.id}/production`, 'PATCH', data)
                this.$state = {...response}
            }

        },
        getters: {
            date: state => new Date(state.birthday),
            dateEntry: state => new Date(state.entryDate),
            getAddress: state => state.address.address,
            getBrith: state =>
                `${state.date.getFullYear()}-${state.getMonth}-${state.getDate}`,
            getCity: state => state.address.city,
            getCountry: state => state.address.country,
            getDate: state =>
                (state.date.getDate() < 9
                    ? `0${state.date.getDate()}`
                    : state.date.getDate()),
            getDateEntry: state =>
                (state.dateEntry.getDate() < 9
                    ? `0${state.dateEntry.getDate()}`
                    : state.dateEntry.getDate()),
            getEmail: state => state.address.email,
            getEmbRoles: state => state.embRoles.roles,
            getHierarchieRole: state => state.embRoles.roles.filter(role => baseHierarchie.includes(role)),
            getAccountingRole: state => state.embRoles.roles.filter(role => baseAccounting.includes(role)),
            getHrRole: state => state.embRoles.roles.filter(role => baseHr.includes(role)),
            getItRole: state => state.embRoles.roles.filter(role => baseIt.includes(role)),
            getLogisticsRole: state => state.embRoles.roles.filter(role => baseLogistics.includes(role)),
            getMaintenanceRole: state => state.embRoles.roles.filter(role => baseMaintenance.includes(role)),
            getManagementRole: state => state.embRoles.roles.filter(role => baseManagement.includes(role)),
            getProductionRole: state => state.embRoles.roles.filter(role => baseProduction.includes(role)),
            getProjectRole: state => state.embRoles.roles.filter(role => baseProject.includes(role)),
            getPurchaseRole: state => state.embRoles.roles.filter(role => basePurchase.includes(role)),
            getQualityRole: state => state.embRoles.roles.filter(role => baseQuality.includes(role)),
            getSellingRole: state => state.embRoles.roles.filter(role => baseSelling.includes(role)),
            getEntryDate: state =>
                `${state.dateEntry.getFullYear()}-${state.getMonthEntry}-${state.getDateEntry}`,
            getMonth: state =>
                (state.date.getMonth() < 9
                    ? `0${state.date.getMonth() + 1}`
                    : state.date.getMonth() + 1),
            getMonthEntry: state =>
                (state.dateEntry.getMonth() < 9
                    ? `0${state.dateEntry.getMonth() + 1}`
                    : state.dateEntry.getMonth() + 1),
            getPhone: state => state.address.phoneNumber,
            getPostal: state => state.address.zipCode,
            getGetterFilter: state => state.getterFilter

        },
        state: () => ({...employee})
    })()
}
