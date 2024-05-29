<script setup>
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import AppSuspense from '../../../AppSuspense.vue'
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
    import AppWorkflowShow from '../../../workflow/AppWorkflowShow.vue'
    import AppCustomerOrderShow from './tabs/AppCustomerOrderShow.vue'
    import AppBtn from '../../../AppBtn.vue'
    import api from '../../../../api'
    import useUser from '../../../../stores/security'

    import {useCustomerOrderStore} from '../../../../stores/customer/customerOrder'
    import {useRoute, useRouter} from 'vue-router'
    import {computed, onBeforeMount, onBeforeUpdate, ref} from 'vue'
    import useOptions from '../../../../stores/option/options'
    import AppCustomerOrderInlist from './bottom/AppCustomerOrderInlist.vue'

    const route = useRoute()
    const router = useRouter()
    const user = useUser()
    const isAdmin = user.isSellingAdmin
    const fetchCustomerOrderStore = useCustomerOrderStore()
    const fetchCompaniesOptions = useOptions('companies')

    const idCustomerOrder = Number(route.params.id)
    const iriCustomerOrder = ref('')
    const beforeMountDataLoaded = ref(false)
    const keyTitle = ref(0)
    const modeDetail = ref(true)
    const isFullScreen = ref(false)
    const keyTabs = ref(0)

    // console.log('idCustomerOrder', idCustomerOrder)

    const companiesOptions = computed(() =>
        fetchCompaniesOptions.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))
    const kindOptions = [
        {text: 'El', value: 'El'},
        {text: 'Prototype', value: 'Prototype'},
        {text: 'Série', value: 'Série'},
        {text: 'Pièce de rechange', value: 'Pièce de rechange'}
    ]
    const optionsOrderFamily = computed(() => fetchCustomerOrderStore.orderFamilyOptions())
    //console.log('optionsOrderFamily', optionsOrderFamily.value)
    const fieldsGenerality = computed(() => {
        let addresseFilter = null
        let contactFilter = null
        if (typeof generalityData.value.wrap1.customer !== 'undefined') {
            // on peut alors filtrer les types de commandes, les adresses de livraison et les contacts du client
            // Le filtre des commandes se fait dans le store
            // Le filtre des adresses de livraison et des contacts se fait ici via les paramètres du multiselect-fetch
            addresseFilter = {field: 'customer', value: generalityData.value.wrap1.customer}
            contactFilter = {field: 'society', value: generalityData.value.wrap1.customer}
        }
        return [
            {
                label: 'Test wrap',
                name: 'wrap1',
                mode: 'wrap',
                wrapWidth: '40%',
                wrapMinWidth: '300px',
                fontSize: '0.6rem',
                children: [
                    {
                        label: 'Compagnie Gérante *',
                        name: 'company',
                        readOnly: !isAdmin,
                        options: {
                            label: value =>
                                companiesOptions.value.find(option => option.type === value)?.text ?? null,
                            options: companiesOptions.value
                        },
                        type: 'select'
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
                    {
                        label: 'Client',
                        name: 'customer',
                        type: 'multiselect-fetch',
                        api: '/api/customers',
                        filteredProperty: 'name',
                        readOnly: !isAdmin,
                        max: 1
                    },
                    {label: 'Référence commande client *', name: 'ref', type: 'text'},
                    {
                        label: 'Type de Produit',
                        name: 'kind',
                        readOnly: !isAdmin,
                        options: {
                            label: value =>
                                kindOptions.find(option => option.type === value)?.text ?? null,
                            options: kindOptions
                        },
                        type: 'select'
                    },
                    {
                        label: 'Adresse de livraison Client *',
                        name: 'destination',
                        type: 'multiselect-fetch',
                        api: '/api/delivery-addresses',
                        filteredProperty: 'name',
                        permanentFilters: [addresseFilter],
                        min: true,
                        max: 1
                    }
                ]
            },
            {
                label: 'wrap2',
                name: 'wrap2',
                mode: 'wrap',
                wrapWidth: '40%',
                wrapMinWidth: '300px',
                fontSize: '0.6rem',
                children: [
                    {
                        label: 'Contact Client',
                        name: 'contact',
                        type: 'multiselect-fetch',
                        api: '/api/customer-contacts',
                        filteredProperty: 'fullName',
                        permanentFilters: [contactFilter],
                        min: true,
                        max: 1
                    },
                    {label: 'Notes', name: 'notes', type: 'textarea'}
                ]
            }
        ]
    })
    // console.log('fieldsGenerality', fieldsGenerality.value)

    const requestDetails = () => {
        modeDetail.value = true
    }
    const requestExploitation = () => {
        modeDetail.value = false
    }
    const activateFullScreen = () => {
        isFullScreen.value = true
    }
    const deactivateFullScreen = () => {
        isFullScreen.value = false
    }
    const generalityData = ref({})
    const order = computed(() => fetchCustomerOrderStore.customerOrder)
    async function updateGeneralityDataFromAppCardShow(data) {
        console.log('current GeneralityData', generalityData.value)
        console.log('data', data)
        //generalityData.value = data
    }
    async function updateGeneralityDataFromApi(data) {
        //Si customer est défini, on le charge afin de pouvoir identifier le type de commande possible
        if (data.customer){
            // console.log('client non encore défini => chargement')
            fetchCustomerOrderStore.selectedCustomer = await api(data.customer, 'GET')
            // console.log('client chargé', fetchCustomerOrderStore.selectedCustomer)
        }
        generalityData.value = {
            wrap1: {
                company: data.company['@id'],
                customer: data.customer,
                kind: data.kind,
                ref: data.ref,
                orderFamily: data.orderFamily,
                destination: data.destination
            },
            wrap2: {
                contact: data.contact,
                notes: data.notes
            }
        }
        // console.log('generalityData', generalityData.value)
    }
    async function updateGeneralityCustomerOrder(){
        //On doit vérifier avant de valider les modifications si le type de commande est de type EDI qu'il n'en existe pas déjà une, auquel cas on ne peut pas modifier le type de commande
        const orderFamily = generalityData.value.orderFamily
        if (orderFamily === 'edi_orders' || orderFamily === 'edi_delfor'){
            const idCustomer = order.value.customer
            const hasEdiCustomerOrders = await fetchCustomerOrderStore.hasActiveEdiOrders(idCustomer, order.value.id)
            if (hasEdiCustomerOrders){
                alert('Il existe déjà une commande de type EDI active pour ce client')
                //On recharge la page
                window.location.reload()
                return
            }
        }
        //On doit vérifier avant de valider les modifications si le type de commande est de type prévisionnelle qu'il n'en existe pas déjà une, auquel cas on ne peut pas modifier le type de commande
        if (orderFamily === 'forecast' || orderFamily === 'edi_delfor'){
            const idCustomer = order.value.customer
            const hasForecastCustomerOrders = await fetchCustomerOrderStore.hasActiveForecastOrders(idCustomer, order.value.id)
            if (hasForecastCustomerOrders){
                alert('Il existe déjà une commande active de type prévisionnelle pour ce client')
                //On recharge la page
                window.location.reload()
                return
            }
        }
        const payload = {
            id: idCustomerOrder,
            SellingOrder: {
                company: generalityData.value.wrap1.company,
                customer: generalityData.value.wrap1.customer,
                kind: generalityData.value.wrap1.kind,
                notes: generalityData.value.wrap2.notes,
                ref: generalityData.value.wrap1.ref,
                orderFamily: generalityData.value.wrap1.orderFamily,
                destination: generalityData.value.wrap1.destination,
                contact: generalityData.value.wrap2.contact
            }
        }
        await fetchCustomerOrderStore.updateSellingOrder(payload)
        keyTabs.value++
    }
    const generalityKey = ref(0)
    onBeforeMount(async () => {
        // console.log('onBeforeMount')
        fetchCompaniesOptions.fetchOp()
        fetchCustomerOrderStore.fetchById(idCustomerOrder).then(async () => {
            // console.log('customerOrder', fetchCustomerOrderStore.customerOrder)
            iriCustomerOrder.value = fetchCustomerOrderStore.customerOrder['@id']
            // console.log('avant onBEforeMount:updateGeneralityData')
            await updateGeneralityDataFromApi(fetchCustomerOrderStore.customerOrder)
            generalityKey.value++
            // console.log('après onBEforeMount:updateGeneralityData', generalityKey.value)
            beforeMountDataLoaded.value = true
        })
    })
    onBeforeUpdate(async () => {
        // console.log('onBeforeUpdate')
        if (iriCustomerOrder.value !== fetchCustomerOrderStore.customerOrder['@id']) {
            iriCustomerOrder.value = fetchCustomerOrderStore.customerOrder['@id']
            // console.log('avant onBeforeUpdate:updateGeneralityData')
            await updateGeneralityDataFromApi(fetchCustomerOrderStore.customerOrder)
            generalityKey.value++
            // console.log('après onBeforeUpdate:updateGeneralityData', generalityKey.value)
        }
    })
    function goToTheList() {
        router.push({name: 'customer-order-list'})
    }
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen v-if="beforeMountDataLoaded">
            <template #gui-left>
                <div :key="`title-${keyTitle}`" class="bg-white border-1 p-1">
                    <div class="d-flex flex-row">
                        <div>
                            <button class="text-dark" @click="goToTheList">
                                <FontAwesomeIcon icon="bullhorn"/>
                            </button>
                            {{ fetchCustomerOrderStore.customerOrder.id }}:
                            <b>{{ fetchCustomerOrderStore.customerOrder.ref }}</b>
                        </div>
                        <AppSuspense>
                            <AppWorkflowShow :workflow-to-show="['selling_order', 'blocker']" :item-iri="iriCustomerOrder"/>
                        </AppSuspense>
                        <span class="ml-auto">
                            <AppBtn :class="{'selected-detail': modeDetail}" label="Détails" icon="eye" variant="secondary" @click="requestDetails"/>
                            <AppBtn :class="{'selected-detail': !modeDetail}" label="Exploitation" icon="industry" variant="secondary" @click="requestExploitation"/>
                        </span>
                    </div>
                </div>
                <div :key="generalityKey" class="row">
                    <AppCardShow id="Generality" :fields="fieldsGenerality" :component-attribute="generalityData" :title="`Informations générales de la commande${isAdmin?' (admin mode)':''}`" @update:model-value="updateGeneralityDataFromAppCardShow" @update="updateGeneralityCustomerOrder"/>
                </div>
            </template>
            <template #gui-bottom>
                <div :class="{'full-screen': isFullScreen}" class="bg-warning-subtle font-small">
                    <div class="full-visible-width">
                        <AppSuspense>
                            <AppCustomerOrderShow v-if="modeDetail" :key="`formtab-${keyTabs}`" class="width100" :order="order"/>
                            <AppCustomerOrderInlist v-else :key="`formlist-${keyTabs}`" class="width100"/>
                        </AppSuspense>
                    </div>
                    <span>
                        <FontAwesomeIcon v-if="isFullScreen" icon="fa-solid fa-magnifying-glass-minus" @click="deactivateFullScreen"/>
                        <FontAwesomeIcon v-else icon="fa-solid fa-magnifying-glass-plus" @click="activateFullScreen"/>
                    </span>
                </div>
            </template>
            <template #gui-right/>
        </AppShowGuiGen>
    </AppSuspense>
</template>
