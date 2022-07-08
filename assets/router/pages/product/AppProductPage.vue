<script setup>
    import AppProductCreate from './AppProductCreate.vue'
    import AppTablePage from '../AppTablePage'
    import {computed} from 'vue-demi'
    import useProducts from '../../../stores/product/products'
    import {useTableMachine} from '../../../machine'

    const title = 'Créer un production'
    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)
    const machineProduct = useTableMachine('machine-product')
    const products = useProducts()

    const fields = computed(() => [
        {
            create: true,
            label: 'Img',
            name: 'img',
            sort: true,
            type: 'text',
            update: false
        },
        {
            create: true,
            label: 'Référence',
            name: 'ref',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            label: 'Famille',
            name: 'famille',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            label: 'Type De Produit',
            name: 'type',
            sort: true,
            type: 'text',
            update: true
        },

        {
            create: true,
            label: 'Client',
            name: 'client',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            label: 'Stocks',
            name: 'stock',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            label: 'Besoins enregistrés',
            name: 'besoin',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            label: 'Date d\'expiration',
            name: 'date',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            label: 'Etat',
            name: 'etat',
            sort: true,
            type: 'text',
            update: true
        }
    ])
</script>

<template>
    <div class="row">
        <AppModal :id="modalId" class="four" :title="title">
            <AppProductCreate/>
        </AppModal>
        <div class="col">
            <AppTablePage
                :fields="fields"
                icon="user-tag"
                :machine="machineProduct"
                :store="products"
                title="La liste de Production">
                <template #cell(etat)>
                    <AppTrafficLight/>
                </template>
                <template #btn>
                    <AppBtn
                        variant="success"
                        data-bs-toggle="modal"
                        :data-bs-target="target">
                        Créer
                    </AppBtn>
                </template>
            </AppTablePage>
        </div>
    </div>
</template>
