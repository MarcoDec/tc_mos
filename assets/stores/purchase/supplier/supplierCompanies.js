import api from '../../../api'
import {defineStore} from 'pinia'
import generateSupplierCompany from './supplierCompany'

export const useSupplierCompaniesStore = defineStore('supplier-companies', {
    actions: {
        async addCompany(companyIRI) {
            const response = await api('/api/supplier-companies', 'POST', {
                company: companyIRI,
                supplier: `/api/suppliers/${this.supplier.id}`
            })
            console.log(response)
            this.fetchBySupplier(this.supplier)
        },
        async fetchBySupplier(supplier) {
            this.supplier = supplier
            this.supplierCompanies = []
            const response = await api(`/api/supplier-companies?supplier=/api/suppliers/${supplier.id}`, 'GET')
            for (const supplierCompany of response['hydra:member']) {
                const item = generateSupplierCompany(supplierCompany, this)
                this.supplierCompanies.push(item)
            }
        },
        async fetchOne(id = 1) {
            const response = await api(`/api/supplier-companies/${id}`, 'GET')
            const item = generateSupplierCompany(response, this)
            this.supplierCompany = item
        },
        async removeOne(id) {
            await api(`/api/supplier-companies/${id}`, 'DELETE')
            this.fetchBySupplier(this.supplier)
        }
    },
    getters: {
    },
    state: () => ({
        supplier: {},
        supplierCompanies: [],
        supplierCompany: {},
        vatMessage: []
    })
})
