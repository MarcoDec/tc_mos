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

    const fields = computed(() => {
        let addresseFilter = null
        let contactFilter = null
        if (typeof generalData.value.customer !== 'undefined') {
            // on peut alors filtrer les types de commandes, les adresses de livraison et les contacts du client
            // Le filtre des commandes se fait dans le store
            // Le filtre des adresses de livraison et des contacts se fait ici via les paramètres du multiselect-fetch
            addresseFilter = {field: 'customer', value: generalData.value.customer}
            contactFilter = {field: 'society', value: generalData.value.customer}
        }
        return [
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
                permanentFilters: [addresseFilter],
                min: true,
                max: 1
            },
            {
                label: 'Contact Client',
                name: 'contact',
                type: 'multiselect-fetch',
                api: '/api/customer-contacts',
                filteredProperty: 'fullName',
                permanentFilters: [contactFilter],
                min: true,
                max: 1
            }
        ]
    })

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
        if (key === 'customer') {
            selectedCustomer.value = await api(value[key], 'GET')
            storeCustomerOrder.selectedCustomer = selectedCustomer.value
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
        if (typeof generalData.value.customer === 'undefined') {
            window.alert('Veuillez sélectionner un client')
            return
        }
        /* eslint-disable require-atomic-updates */
        customerOrderData.value.customer = generalData.value.customer
        if (typeof generalData.value.orderFamily === 'undefined') {
            window.alert('Veuillez sélectionner un type de commande')
            return
        }
        /* eslint-disable require-atomic-updates */
        customerOrderData.value.orderFamily = generalData.value.orderFamily
        //Si orderFamily est de type DELFOR et qu'il en existe déjà en base associé au client alors il faut bloquer l'enregistrement et afficher un message d'erreur
        if (customerOrderData.value.orderFamily === 'edi_delfor') {
            const response2 = await api(`/api/selling-orders?orderFamily=edi_delfor&deleted=false&customer=${customerOrderData.value.customer}`, 'GET')
            console.log('response commandes DELFOR', response2)
            if (response2['hydra:totalItems'] > 0) {
                window.alert('Il existe déjà une commandes DELFOR active pour ce client. Il est interdit d\'en avoir 2 actives.')
                return
            }
            // On force la propriété kind en 'Série'
            customerOrderData.value.kind = 'Série'
        }
        if (typeof generalData.value.ref === 'undefined') {
            window.alert('Veuillez saisir une référence')
            return
        }
        /* eslint-disable require-atomic-updates */
        customerOrderData.value.ref = generalData.value.ref
        //Si une commande client avec cette référence existe déjà alors il faut bloquer l'enregistrement et afficher un message d'erreur
        const response = await api(`/api/selling-orders?ref=${customerOrderData.value.ref}&deleted=false&customer=${customerOrderData.value.customer}`, 'GET')
        if (response['hydra:totalItems'] > 0) {
            window.alert('Il existe déjà une commande client avec cette référence pour ce client. Veuillez en choisir une autre.')
            return
        }
        if (typeof generalData.value.destination === 'undefined') {
            window.alert('Veuillez sélectionner une adresse de livraison')
            return
        }
        /* eslint-disable require-atomic-updates */
        customerOrderData.value.destination = generalData.value.destination
        if (typeof generalData.value.contact !== 'undefined') {
            //Ajout d'un escape eslint require-atomic-updates
            /* eslint-disable require-atomic-updates */
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
