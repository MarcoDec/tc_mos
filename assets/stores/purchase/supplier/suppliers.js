import api from '../../../api'
import {defineStore} from 'pinia'
import generateSupplier from './supplier'

export const useSuppliersStore = defineStore('suppliers', {
    actions: {
        async fetchOne(id = 1) {
            const response = await api(`/api/suppliers/${id}`, 'GET')
            const item = generateSupplier(response, this)
            this.supplier = item
        },
        async fetchVatMessage() {
            const response = await api('/api/vat-messages', 'GET')
            this.vatMessage = response['hydra:member']
        }

    },
    getters: {
        getSupplier: state => ({
            //administeredBy: state.supplier.administeredBy,
            accountingAccount: state.supplier.accountingAccount,
            address: state.supplier.address,
            administeredBy: state.supplier.administeredBy,
            ar: state.supplier.ar,
            attachments: state.supplier.attachments,
            confidenceCriteria: state.supplier.confidenceCriteria,
            copper: state.supplier.copper,
            currency: state.supplier.currency,
            forceVat: state.supplier.forceVat,
            id: state.supplier.id,
            incoterms: state.supplier.incoterms,
            invoiceMin: state.supplier.invoiceMin,
            language: state.supplier.language,
            managedCopper: state.supplier.managedCopper,
            managedProduction: state.supplier.managedProduction,
            managedQuality: state.supplier.managedQuality,
            name: state.supplier.name,
            notes: state.supplier.notes,
            openOrdersEnabled: state.supplier.openOrdersEnabled,
            orderMin: state.supplier.orderMin,
            society: state.supplier.society,
            vat: state.supplier.vat,
            vatMessage: state.supplier.vatMessage
        })
    },
    state: () => ({
        supplier: {},
        suppliers: {},
        vatMessage: []
    })
})
