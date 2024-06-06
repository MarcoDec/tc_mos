<script setup>
    import AppModal from '../../../../../modal/AppModal.vue'
    import AppFormJS from '../../../../../form/AppFormJS'
    import {computed, ref} from 'vue'
    import api from '../../../../../../api'
    import {Modal} from 'bootstrap'
    import Measure from './measure'
    import {useCustomerOrderItemsStore} from '../../../../../../stores/customer/customerOrderItems'

    const emits = defineEmits(['updated'])
    const props = defineProps({
        customer: {default: () => ({}), type: Object},
        order: {default: () => ({}), type: Object},
        optionsUnit: {default: () => ({}), type: Object},
        optionsCurrency: {default: () => ({}), type: Object}
    })
    // console.log('props', props)
    const storeCustomerOrderItems = useCustomerOrderItemsStore()
    const measure = new Measure(props.optionsUnit)
    const fixedFormKey = ref(0)
    const customerOrderItemFixedCreateModal = ref(null)
    const localFixedData = ref({
        product: null,
        component: null,
        requestedQuantity: {
            code: null,
            value: null
        },
        confirmedQuantity: {
            code: null,
            value: null
        },
        requestedDate: null,
        confirmedDate: null,
        price: {
            code: null,
            value: null
        }
    })
    const fieldsOrderItem = computed(() => [{
            label: 'Produit',
            name: 'product',
            info: 'Si un produit est sélectionné, la quantité minimale de livraison définie sur la fiche produit sera affectée aux quantités demandées et confirmées.',
            type: 'multiselect-fetch',
            api: '/api/products',
            filteredProperty: 'code',
            max: 1
        },
        {
            label: 'Composant',
            name: 'component',
            info: 'Si un composant est sélectionné, l\'unité associée sera affectée aux quantités demandées et confirmées.',
            type: 'multiselect-fetch',
            api: '/api/components',
            filteredProperty: 'code',
            max: 1
        },
        {
            label: 'Quantité souhaitée *',
            name: 'requestedQuantity',
            info: 'Obligatoire\nSi un produit est sélectionnée, cette quantité doit être supérieure à la quantité minimale de livraison définie sur la fiche produit.\n'
                + 'Lorsque la quantité change le prix unitaire est récupéré automatiquement de la grille tarifaire associée au produit/composant et au client',
            filter: true,
            min: true,
            measure: {
                code: {
                    label: 'Code',
                    name: 'requestedQuantity.code',
                    options: {
                        label: value =>
                            props.optionsUnit.find(option => option.type === value)?.text ?? null,
                        options: props.optionsUnit
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'requestedQuantity.value',
                    type: 'number',
                    step: 0.1
                }
            },
            type: 'measure'
        },
        {
            label: 'Quantité confirmée',
            name: 'confirmedQuantity',
            info: 'Si un produit est sélectionnée, cette quantité doit être supérieure à la quantité minimale de livraison définie sur la fiche produit.\n'
                + 'Lorsque la quantité change le prix unitaire est récupéré automatiquement de la grille tarifaire associée au produit/composant et au client',
            filter: true,
            min: true,
            measure: {
                code: {
                    label: 'Code',
                    name: 'confirmedQuantity.code',
                    options: {
                        label: value =>
                            props.optionsUnit.find(option => option.type === value)?.text ?? null,
                        options: props.optionsUnit
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'confirmedQuantity.value',
                    type: 'number',
                    step: 0.1
                }
            },
            type: 'measure'
        },
        {
            label: 'Date de livraison souhaitée *',
            name: 'requestedDate',
            info: 'Obligatoire',
            type: 'date'
        },
        {
            label: 'Date de livraison confirmée',
            name: 'confirmedDate',
            type: 'date'
        },
        {
            label: 'Prix Unitaire',
            name: 'price',
            type: 'measure',
            info: 'Le prix unitaire est récupéré automatiquement de la grille tarifaire associée au produit/composant et au client.\n'
                + 'Il est calculé en fonction de la quantité confirmée si la quantité est non nulle, sinon il est calculé à partir de la quantité souhaitée.\n'
                + 'La valeur du prix unitaire peut être modifiée manuellement.',
            measure: {
                code: {
                    label: 'Code',
                    name: 'price.code',
                    options: {
                        label: value =>
                            props.optionsCurrency.find(option => option.type === value)?.text ?? null,
                        options: props.optionsCurrency
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'price.value',
                    type: 'number',
                    step: 0.0001
                }
            }
        }])
    async function updateFixedLocalData(value, localData) {
        if (typeof localData === 'undefined') {
            return
        }
        const initialLocalData = {...localData}
        Object.assign(localData, value);
        // localData = value

        if (value.product && value.product !== initialLocalData.product) {
            localData.value.component = null
            await api(value.product, 'GET').then(async response => {
                measure.setQuantityToMinDelivery(localData.value, response)
                await Measure.getAndSetProductPrice(response, props.customer, props.order, localData.value.requestedQuantity, localData, fixedFormKey)
            })
            return
        }
        if (value.component && value.component !== initialLocalData.component) {
            localData.value.product = null
            await api(value.component, 'GET').then(async response => {
                Measure.setQuantityToUnit(localData.value, response)
                await measure.getAndSetComponentPrice(response, props.customer, props.order, localData.value.requestedQuantity, localData, fixedFormKey)
            })
            return
        }
        if (value.confirmedQuantity.value && value.confirmedQuantity.value !== initialLocalData.confirmedQuantity.value) {
            // Si un produit a été sélectionné on récupère le prix unitaire associé au produit et au client
            if (value.product) {
                await Measure.getAndSetProductPrice(value.product, props.customer, props.order, localData.value.confirmedQuantity, localData, fixedFormKey)
                return
            }
            if (value.component) {
                await Measure.getAndSetComponentPrice(value.component, props.customer, props.order, localData.value.confirmedQuantity, localData, fixedFormKey)
                return
            }
        }
        if (value.requestedQuantity.value && value.requestedQuantity.value !== initialLocalData.requestedQuantity.value) {
            // Si un produit a été sélectionné on récupère le prix unitaire associé au produit et au client
            if (value.product) {
                await measure.getAndSetProductPrice(value.product, props.customer, props.order, localData.value.requestedQuantity, localData, fixedFormKey)
                return
            }
            if (value.component) {
                await measure.getAndSetComponentPrice(value.component, props.customer, props.order, localData.value.requestedQuantity, localData, fixedFormKey)
            }
        }
    }
    async function addFixedItem() {
        //On ajoute le champ parentOrder
        localFixedData.value.parentOrder = props.order['@id']
        //On ajoute le type d'item isForecast à false
        localFixedData.value.isForecast = false
        //On remplace la valeur du code qui contient actuellement l'id de l'unité par le code de l'unité
        const requestedUnit = props.optionsUnit.find(unit => unit.value === localFixedData.value.requestedQuantity.code)
        const confirmedUnit = props.optionsUnit.find(unit => unit.value === localFixedData.value.confirmedQuantity.code)
        //Si c'est un produit on positionne la clé item avec la valeur de la clé produit, sinon on positionne la clé item avec la valeur de la clé component
        if (localFixedData.value.product) localFixedData.value.item = localFixedData.value.product
        else localFixedData.value.item = localFixedData.value.component
        localFixedData.value.requestedQuantity.code = requestedUnit.text
        //eslint-disable-next-line require-atomic-updates
        localFixedData.value.confirmedQuantity.code = confirmedUnit.text
        await storeCustomerOrderItems.add(localFixedData.value)
        //On ferme la modale
        if (customerOrderItemFixedCreateModal.value) {
            const modalElement = customerOrderItemFixedCreateModal.value.$el
            const bootstrapModal = Modal.getInstance(modalElement)
            bootstrapModal.hide()
        }
        //On réinitalise les données locales
        // eslint-disable-next-line require-atomic-updates
        localFixedData.value = {}
        emits.push('updated')
    }
</script>

<template>
    <AppModal
        id="modalAddNewOrderItem"
        ref="customerOrderItemFixedCreateModal"
        title="Ajouter Item en Ferme">
        <AppFormJS
            id="formAddNewOrderItem"
            :key="fixedFormKey"
            :model-value="localFixedData"
            :fields="fieldsOrderItem"
            submit-label="Ajouter"
            @update:model-value="value => updateFixedLocalData(value, localFixedData)"
            @submit="addFixedItem"/>
    </AppModal>
</template>
