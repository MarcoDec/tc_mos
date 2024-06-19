<script setup>
    import {computed, defineEmits, defineProps, ref} from 'vue'
    import AppFormJS from '../../../../form/AppFormJS.js'
    import useUser from '../../../../../stores/security'
    import {usePurchaseOrderStore} from "../../../../../stores/purchase/order/purchaseOrder"
    import api from '../../../../../api'

    defineProps({
        title: {required: true, type: String},
        target: {required: true, type: String},
        modalId: {required: true, type: String}
    })
    const emits = defineEmits(['created'])

    const user = useUser()
    const storeSupplierOrder = usePurchaseOrderStore()

    const violations = ref([])
    let success = []
    const isPopupVisible = ref(false)
    const isCreatedPopupVisible = ref(false)
    const currentCompany = user.company
    const generalData = ref({})
    const selectedSupplier = ref(null)
    const optionsOrderFamily = computed(() => storeSupplierOrder.orderFamilyOptions())

    const fields = computed(() => {
        let addresseFilter = null
        let contactFilter = null
        if (typeof generalData.value.supplier !== 'undefined') {
            // on peut alors filtrer les types de commandes, les adresses de livraison et les contacts du client
            // Le filtre des commandes se fait dans le store
            // Le filtre des adresses de livraison et des contacts se fait ici via les paramètres du multiselect-fetch
            addresseFilter = {field: 'sypplier', value: generalData.value.supplier}
            contactFilter = {field: 'society', value: generalData.value.supplier}
        }
        return [
            {
                label: 'Fournisseur *',
                name: 'supplier',
                type: 'multiselect-fetch',
                api: '/api/suppliers',
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
                label: 'Site de livraison *',
                name: 'deliveryCompany',
                type: 'multiselect-fetch',
                api: '/api/companies',
                filteredProperty: 'name',
                min: true,
                max: 1
            },
            {
                label: 'Contact Fournisseur',
                name: 'contact',
                type: 'multiselect-fetch',
                api: '/api/supplier-contacts',
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
        if (key === 'supplier') {
            selectedSupplier.value = await api(value[key], 'GET')
            storeSupplierOrder.selectedSupplier = selectedSupplier.value
        }
    }

    const supplierOrderData = ref({})

    function resetForm() {
        generalData.value = {}
        supplierOrderData.value = {
            supplier: null,
            orderFamily: 'fixed',
            ref: '',
            deliveryCompany: null,
            contact: null,
            company: currentCompany
        }
    }

    resetForm()
    async function supplierFormCreate(){
        if (typeof generalData.value.supplier === 'undefined') {
            window.alert('Veuillez sélectionner un fournisseur')
            return
        }
        /* eslint-disable require-atomic-updates */
        supplierOrderData.value.supplier = generalData.value.supplier
        if (typeof generalData.value.orderFamily === 'undefined') {
            window.alert('Veuillez sélectionner un type de commande')
            return
        }
        /* eslint-disable require-atomic-updates */
        supplierOrderData.value.orderFamily = generalData.value.orderFamily
        //Si orderFamily est de type DELFOR et qu'il en existe déjà en base associé au client alors il faut bloquer l'enregistrement et afficher un message d'erreur
        if (supplierOrderData.value.orderFamily === 'edi_delfor') {
            const response2 = await api(`/api/purchase-orders?orderFamily=edi_delfor&deleted=false&supplier=${supplierOrderData.value.supplier}`, 'GET')
            console.log('response commandes DELFOR', response2)
            if (response2['hydra:totalItems'] > 0) {
                window.alert('Il existe déjà une commandes DELFOR active pour ce fournisseur. Il est interdit d\'en avoir 2 actives.')
                return
            }
            // On force la propriété kind en 'Série'
            supplierOrderData.value.kind = 'Série'
        }
        if (typeof generalData.value.ref === 'undefined') {
            window.alert('Veuillez saisir une référence')
            return
        }
        /* eslint-disable require-atomic-updates */
        supplierOrderData.value.ref = generalData.value.ref
        //Si une commande client avec cette référence existe déjà alors il faut bloquer l'enregistrement et afficher un message d'erreur
        const response = await api(`/api/purchase-orders?ref=${supplierOrderData.value.ref}&deleted=false&supplier=${supplierOrderData.value.supplier}`, 'GET')
        if (response['hydra:totalItems'] > 0) {
            window.alert('Il existe déjà une commande fournisseur avec cette référence pour ce client. Veuillez en choisir une autre.')
            return
        }
        if (typeof generalData.value.deliveryCompany === 'undefined') {
            window.alert('Veuillez sélectionner une adresse de livraison')
            return
        }
        /* eslint-disable require-atomic-updates */
        supplierOrderData.value.deliveryCompany = generalData.value.deliveryCompany
        if (typeof generalData.value.contact !== 'undefined') {
            //Ajout d'un escape eslint require-atomic-updates
            /* eslint-disable require-atomic-updates */
            supplierOrderData.value.contact = generalData.value.contact
        }
        try {
            await storeSupplierOrder.addPurchaseOrder(supplierOrderData.value)
            isPopupVisible.value = false
            isCreatedPopupVisible.value = true
            success = 'Commande créée'
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
        <AppFormJS id="purchase_order_create_form" :fields="fields" :violations="violations" @update:model-value="generalForm"/>
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
                @click="supplierFormCreate">
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
