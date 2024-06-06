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
    const forecastFormKey = ref(0)
    const localForecastData = ref({
        product: null,
        component: null,
        requestedQuantity: {
           code: null,
           value: null
        },
        requestedDate: null,
        price: {
           code: null,
           value: null
        }})
    const customerOrderItemForecastCreateModal = ref(null)

    const fieldsOpenOrderItem = computed(() => [
        {
            label: 'Produit',
            name: 'product',
            info: 'Si un produit est sélectionné, la quantité minimale de livraison définie sur la fiche produit sera affectée aux quantités demandées.',
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
            label: 'Quantité souhaitée',
            name: 'requestedQuantity',
            info: 'Obligatoire\nSi un produit est sélectionnée, cette quantité doit être supérieure à la quantité minimale de livraison définie sur la fiche produit.\n'
                + 'Lorsque la quantité change le prix unitaire est récupéré automatiquement de la grille tarifaire associée au produit/composant et au client',
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
            label: 'Date de livraison souhaitée',
            name: 'requestedDate',
            type: 'date'
        },
        {
            label: 'Prix Unitaire',
            name: 'price',
            type: 'measure',
            info: 'Le prix unitaire est récupéré automatiquement de la grille tarifaire associée au produit/composant et au client.\n'
                + 'Il est calculé en fonction de la quantité souhaitée.\n'
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
        }
    ])
    async function updateForecastValue(value, localData) {
        console.log('updateForecastValue')
        if (typeof localData === 'undefined') {
            return
        }
        // localData doit être mise à jour mais comme certaines actions et contrôles doivent être
        // effectués en fonction de la valeur initiale de localData, on crée une copie de localData
        // pour pouvoir comparer les valeurs initiales et les valeurs mises à jour
        const initialLocalData = {...localData}
        Object.assign(localData, value);
        // Si un produit a été sélectionné
        if (value.product && value.product !== initialLocalData.product) {
            console.log('---modification produit')
            // On remet à zéro le composant
            localData.component = null
            console.log('------chargement produit', value.product)
            // On récupère les données du produit
            await api(value.product, 'GET').then(async response => {
                // On met à jour la quantité requise en fonction de la quantité minimale de livraison
                console.log('------produit récupéré', response)
                console.log('------récupération de la quantité minimale de livraison')
                measure.setQuantityToMinDelivery(localData, response)
                console.log('------quantité minimale de livraison récupérée', localData.requestedQuantity)
                // On récupère le prix unitaire associé au produit et au client
                console.log('------récupération du prix unitaire')
                await Measure.getAndSetProductPrice(response, props.customer, props.order, localData.requestedQuantity, localData, forecastFormKey)
                console.log('------prix unitaire récupéré')
            })
            return
        }
        if (value.component && value.component !== initialLocalData.component) {
            localData.product = null
            await api(value.component, 'GET').then(async response => {
                measure.setQuantityToUnit(localData, response)
                await measure.getAndSetComponentPrice(response, props.customer, props.order, localData.requestedQuantity, localData, forecastFormKey)
            })
            return
        }
        if (value.requestedQuantity.value && value.requestedQuantity.value !== initialLocalData.requestedQuantity.value) {
            console.log('modification quantité requise', value, localData)
            // Si un produit a été sélectionné on récupère le prix unitaire associé au produit et au client
            if (initialLocalData.product) {
                console.log('un produit est sélectionné', value.product)
                await Measure.getAndSetProductPrice(value.product, props.customer, props.order, localData.requestedQuantity, localData, forecastFormKey)
                return
            }
            if (initialLocalData.component) {
                console.log('un composant est sélectionné')
                await Measure.getAndSetComponentPrice(value.component, props.customer, props.order, localData.requestedQuantity, localData, forecastFormKey)
            }
        }
    }
    async function addForecastItem() {
        //On ajoute le champ parentOrder
        localForecastData.value.parentOrder = props.order['@id']
        //On ajoute le type d'item isForecast
        localForecastData.value.isForecast = true
        //On remplace la valeur du code qui contient actuellement l'id de l'unité par le code de l'unité
        const requestedUnit = props.optionsUnit.find(unit => unit.value === localForecastData.value.requestedQuantity.code)
        //Si c'est un produit on positionne la clé item avec la valeur de la clé produit, sinon on positionne la clé item avec la valeur de la clé component
        if (localForecastData.value.product) localForecastData.value.item = localForecastData.value.product
        else localForecastData.value.item = localForecastData.value.component
        if (localForecastData.value.confirmedQuantity) {
            //on retire la quantité confirmée
            delete localForecastData.value.confirmedQuantity
        }
        //On remplace la valeur du code qui contient actuellement l'id de l'unité par le code de l'unité
        await api(localForecastData.value.price.code, 'GET').then(currency => {
            localForecastData.value.price.code = currency.code
        })
        // eslint-disable-next-line require-atomic-updates
        localForecastData.value.requestedQuantity.code = requestedUnit.text
        await storeCustomerOrderItems.add(localForecastData.value)
        //On ferme la modale
        if (customerOrderItemForecastCreateModal.value) {
            const modalElement = customerOrderItemForecastCreateModal.value.$el
            const bootstrapModal = Modal.getInstance(modalElement)
            bootstrapModal.hide()
        }
        //On réinitalise les données locales
        // on désactive require-atomic-updates car on ne peut pas utiliser await dans une fonction non asynchrone
        // eslint-disable-next-line require-atomic-updates
        localForecastData.value = {}
        //On rafraichit les données du tableau
        emits.push('updated')
    }
</script>

<template>
    <AppModal
        id="modalAddNewForecastItem"
        ref="customerOrderItemForecastCreateModal"
        title="Ajouter Item en Prévisionnel">
        <AppFormJS
            id="formAddNewOrderItem"
            :key="forecastFormKey"
            :model-value="localForecastData"
            :fields="fieldsOpenOrderItem"
            submit-label="Ajouter"
            @update:model-value="value => updateForecastValue(value, localForecastData)"
            @submit="addForecastItem"/>
    </AppModal>
</template>
