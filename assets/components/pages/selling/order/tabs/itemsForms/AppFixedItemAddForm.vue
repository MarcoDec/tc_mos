<script setup>
    import {computed, ref} from 'vue'
    import {useCustomerOrderItemsStore} from '../../../../../../stores/customer/customerOrderItems'
    import AppGenOrderItemForm from './AppGenOrderItemForm.vue'

    const emits = defineEmits(['updated', 'closed', 'submit'])
    const props = defineProps({
        customer: {default: () => ({}), type: Object},
        modalId: {required: true, type: String},
        order: {default: () => ({}), type: Object},
        optionsUnit: {default: () => ({}), type: Object},
        optionsCurrency: {default: () => ({}), type: Object}
    })
    const storeCustomerOrderItems = useCustomerOrderItemsStore()
    const localFixedData = ref({
        product: null,
        component: null,
        requestedQuantity: {
            code: '/api/units/1',
            value: 1.0
        },
        confirmedQuantity: {
            code: '/api/units/1',
            value: 0
        },
        requestedDate: null,
        confirmedDate: null,
        price: {
            code: '/api/currencies/1',
            value: 0.0
        }
    })
    const fieldsOrderItem = computed(() => [
        {
            label: 'Produit',
            name: 'product',
            info: 'Si un produit est sélectionné, la quantité minimale de livraison définie sur la fiche produit sera affectée aux quantités demandées et confirmées.',
            type: 'multiselect-fetch',
            api: '/api/products',
            filteredProperty: 'code',
            permanentFilters: [
                {field: 'productCustomers.customer', value: props.customer['@id']},
                {field: 'kind', value: props.order.kind}
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
            label: 'Quantité souhaitée *',
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
            label: 'Quantité confirmée',
            name: 'confirmedQuantity',
            info: 'Si un produit est sélectionnée, cette quantité doit être supérieure à la quantité minimale de livraison définie sur la fiche produit.\n'
                + 'Lorsque la quantité change le prix unitaire est récupéré automatiquement de la grille tarifaire associée au produit/composant et au client',
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
        }
    ])
</script>

<template>
    <AppGenOrderItemForm
        :customer="customer"
        :fields="fieldsOrderItem"
        :form-data="localFixedData"
        form-id="formAddNewFixedOrderItem"
        :modal-id="modalId"
        mode="add"
        :options-currency="optionsCurrency"
        :options-unit="optionsUnit"
        :order="order"
        :store="storeCustomerOrderItems"
        title="Ajouter Item en ferme"
        variant="fixed"
        @updated="value => emits('updated', value)"
        @closed="() => emits('closed')"
        @submit="() => emits('submit')"/>
</template>
