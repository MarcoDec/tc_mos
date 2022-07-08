<script setup>
    import AppManufacturingOrderCreate from './AppManufacturingOrderCreate.vue'
    import AppTablePage from '../AppTablePage'
    import {computed} from 'vue-demi'
    import useManufacturingOrders from '../../../stores/manufacturingOrder/manufacturingOrders'
    import {useTableMachine} from '../../../machine'

    const title = 'Créer un OF'
    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)
    const machineManufacturingOrders = useTableMachine(
        'machine-manufacturing-orders'
    )
    const manufacturingOrders = useManufacturingOrders()

    const fields = computed(() => [
        {
            create: true,
            label: 'N OF',
            name: 'numero',
            sort: true,
            type: 'text',
            update: false
        },
        {
            create: true,
            label: 'Indice',
            name: 'indice',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            label: 'Quantite',
            name: 'quantite',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Quantite fabriquée',
            name: 'quantiteF',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            label: 'Produit',
            name: 'produit',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            label: 'Usine',
            name: 'usine',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            label: 'Commande client',
            name: 'commande',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            label: 'Compagnie',
            name: 'compagnie',
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
            <AppManufacturingOrderCreate/>
        </AppModal>
        <div class="col">
            <AppTablePage
                :fields="fields"
                icon="user-tag"
                :machine="machineManufacturingOrders"
                :store="manufacturingOrders"
                title="La liste de OF">
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
