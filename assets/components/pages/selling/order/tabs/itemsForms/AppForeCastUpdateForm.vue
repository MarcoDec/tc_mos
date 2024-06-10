<script setup>
    import {computed} from 'vue'
    import {useCustomerOrderItemsStore} from '../../../../../../stores/customer/customerOrderItems'
    import AppGenOrderItemForm from './AppGenOrderItemForm.vue'
    import AppSuspense from '../../../../../AppSuspense.vue'

    const emits = defineEmits(['updated', 'closed'])
    const props = defineProps({
        customer: {default: () => ({}), type: Object},
        modalId: {required: true, type: String},
        order: {default: () => ({}), type: Object},
        optionsUnit: {default: () => ({}), required: true, type: Object},
        optionsCurrency: {default: () => ({}), required: true, type: Object}
    })
    console.log('props', props)
    const storeCustomerOrderItems = useCustomerOrderItemsStore()
    const localForecastData = computed(() => storeCustomerOrderItems.currentItem.value)
    const fieldsOpenOrderItem = computed(() => [
        {
            label: 'Produit',
            name: 'product',
            info: 'Si un produit est sélectionné, la quantité minimale de livraison définie sur la fiche produit sera affectée aux quantités demandées.',
            type: 'multiselect-fetch',
            api: '/api/products',
            filteredProperty: 'code',
            permanentFilters: [
                {field: 'productCustomers.customer', value: props.customer['@id']}
            ],
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
    console.log('localForecastData', localForecastData.value)
    function onModalClose() {
        emits('closed')
    }
</script>

<template>
    <AppSuspense>
        <AppGenOrderItemForm
            :customer="customer"
            :fields="fieldsOpenOrderItem"
            :form-data="localForecastData"
            :modal-id="modalId"
            mode="edit"
            :order="order"
            :options-unit="optionsUnit"
            :options-currency="optionsCurrency"
            :store="storeCustomerOrderItems"
            :title="`Modifier item prévisionnel du ${localForecastData.requestedDate}`"
            btn-label="Modifier"
            @closed="onModalClose"
            @updated="value => emits('updated', value)"/>
    </AppSuspense>
</template>
