<script setup>
    import {computed, defineEmits, defineProps, ref} from 'vue'
    import AppFormJS from '../../../form/AppFormJS.js'
    import useUser from '../../../../stores/security'
    import {useCustomerOrderStore} from '../../../../stores/customer/customerOrder'
    import api from '../../../../api'

    defineProps({
        title: {required: true, type: String},
        target: {required: true, type: String},
        modalId: {required: true, type: String}
    })
    const emits = defineEmits(['created'])

    const user = useUser()
    const storeCustomerOrder = useCustomerOrderStore()

    const violations = ref([])
    let success = []
    const isPopupVisible = ref(false)
    const isCreatedPopupVisible = ref(false)
    const currentCompany = user.company
    const generalData = ref({})
    const selectedCustomer = ref(null)
    const optionsOrderFamily = computed(() => {
        //TODO
        return storeCustomerOrder.orderFamilyOptions()
    })
    const fields = computed(() => [
        {
            label: 'Client *',
            name: 'customer',
            type: 'multiselect-fetch',
            api: '/api/customers',
            filteredProperty: 'name',
            min: true,
            max: 1
        },
        {
            label: 'Type de commande *',
            name: 'orderFamily',
            options: {
                label: value => optionsOrderFamily.value.find(option => option.type === value)?.text ?? null,
                options: optionsOrderFamily.value
            },
            type: 'select'
        },
        {label: 'Référence*', name: 'ref', type: 'text'},
        {
            label: 'Adresse de livraison Client *',
            name: 'destination',
            type: 'multiselect-fetch',
            api: '/api/delivery-addresses',
            filteredProperty: 'name',
            min: true,
            max: 1
        },
        {
            label: 'Contact Client',
            name: 'contact',
            type: 'multiselect-fetch',
            api: '/api/customers-contacts',
            filteredProperty: 'name',
            min: true,
            max: 1
        }
    ])

    async function generalForm(value) {
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(generalData.value, key)) {
            if (typeof value[key] === 'object') {
                if (typeof value[key].value !== 'undefined') {
                    const inputValue = parseFloat(value[key].value)
                    generalData.value[key] = {...generalData.value[key], value: inputValue}
                }
                if (typeof value[key].code !== 'undefined') {
                    const inputCode = value[key].code
                    generalData.value[key] = {...generalData.value[key], code: inputCode}
                }
            } else {
                generalData.value[key] = value[key]
            }
        } else {
            generalData.value[key] = value[key]
        }
        console.log('generalData', generalData.value)
        if (key === 'customer') {
            selectedCustomer.value = await api(value[key], 'GET')
            storeCustomerOrder.selectedCustomer = await api(value[key], 'GET')
            console.log('selectedCustomer', storeCustomerOrder.selectedCustomer)
            console.log('customerWithIntegratedEdi', storeCustomerOrder.customerWithIntegratedEdi())
            console.log('storeCustomerOrder.orderFamilyOptions', storeCustomerOrder.orderFamilyOptions())
        }
    }

    const customerOrderData = ref({})

    function resetForm() {
        generalData.value = {}
        customerOrderData.value = {
            customer: null,
            orderFamily: 'fixed',
            ref: '',
            destination: null,
            contact: null,
            company: currentCompany
        }
    }

    resetForm()
    async function customerFormCreate(){
        if (typeof generalData.value.customer !== 'undefined') {
            customerOrderData.value.customer = generalData.value.customer
        } else {
            window.alert('Veuillez sélectionner un client')
            return
        }
        if (typeof generalData.value.orderFamily !== 'undefined') {
            customerOrderData.value.orderFamily = generalData.value.orderFamily
            //Si orderFamily est de type DELFOR et qu'il en existe déjà en base associé au client alors il faut bloquer l'enregistrement et afficher un message d'erreur
            if (customerOrderData.value.orderFamily === 'edi_delfor') {
                const response = await api('/api/selling-orders?orderFamily=edi_delfor&deleted=false&customer='+customerOrderData.value.customer, 'GET')
                console.log('response commandes DELFOR', response)
                if (response['hydra:totalItems']>0) {
                    window.alert('Il existe déjà une commandes DELFOR active pour ce client. Il est interdit d\'en avoir 2 actives.')
                    return
                }
            }
        } else {
            window.alert('Veuillez sélectionner un type de commande')
            return
        }
        if (typeof generalData.value.ref !== 'undefined') {
            customerOrderData.value.ref = generalData.value.ref
            //Si une commande client avec cette référence existe déjà alors il faut bloquer l'enregistrement et afficher un message d'erreur
            const response = await api('/api/selling-orders?ref='+customerOrderData.value.ref+'&deleted=false&customer='+customerOrderData.value.customer, 'GET')
            if (response['hydra:totalItems']>0) {
                window.alert('Il existe déjà une commande client avec cette référence pour ce client. Veuillez en choisir une autre.')
                return
            }
        }
        if (typeof generalData.value.destination !== 'undefined') {
            customerOrderData.value.destination = generalData.value.destination
        }
        if (typeof generalData.value.contact !== 'undefined') {
            customerOrderData.value.contact = generalData.value.contact
        }
        try {
            await storeCustomerOrder.addCustomerOrder(customerOrderData.value)
            isPopupVisible.value = false
            isCreatedPopupVisible.value = true
            success = 'Commande crée'
            // Remise à zéro des données
            resetForm()
            violations.value = []
            emits('created')
        } catch (error) {
            violations.value = error
            isPopupVisible.value = true
            isCreatedPopupVisible.value = false
            console.error('violations', violations.value)
        }
    }
</script>

<template>
    <AppModal :id="modalId" class="four" :title="title">
        <AppFormJS id="order_create_form" :fields="fields" :violations="violations" @update:model-value="generalForm"/>
        <ul v-if="isPopupVisible" class="alert alert-danger" role="list">
            <li>{{ violations }}</li>
        </ul>
        <ul v-if="isCreatedPopupVisible" class="alert alert-success" role="list">
            <li>{{ success }}</li>
        </ul>
        <template #buttons>
            <AppBtn
                variant="success"
                label="Créer"
                data-bs-toggle="modal"
                :data-bs-target="target"
                @click="customerFormCreate">
                Créer
            </AppBtn>
        </template>
    </AppModal>
</template>

<style>
.cardOrderSupplier {
  border: 6px solid #1d583d;
}
.tab-pane {
    padding: 10px;
}
</style>
