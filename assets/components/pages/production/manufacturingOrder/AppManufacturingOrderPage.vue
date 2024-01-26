<script setup>
    import AppManufacturingOrderCreate from './AppManufacturingOrderCreate.vue'
    import AppTablePage from '../../table/AppTablePage.vue'
    import useManufacturingOrders from '../../../../stores/production/manufacturingOrder/manufacturingOrders'
    import {computed} from 'vue-demi'
    import {useRouter} from 'vue-router'
    import useUser from '../../../../stores/security'
    import {useTableMachine} from '../../../../machine'
    import {AppCardableTable} from "../../../bootstrap-5/app-cardable-collection-table";
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import Fa from "../../../Fa";
    import {ref} from "vue";
    import {Modal} from "bootstrap";
    import AppComponentCreateModal from "../../purchase/component/list/create/AppComponentCreateModal.vue";

    console.log('AppManufacturingOrderPage.vue')

    const router = useRouter()
    const fetchUser = useUser()
    const isProductionWriterOrAdmin = fetchUser.isProductionWriter || fetchUser.isProductionAdmin

    const title = 'Créer un OF'
    const creationSuccess = ref(false)
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
    async function onCreated() {
        await refreshTable()
        if (createModalRef.value) {
            const modalElement = createModalRef.value.$el
            const bootstrapModal = Modal.getInstance(modalElement)
            bootstrapModal.hide()
            creationSuccess.value = true
            setTimeout(() => {
                creationSuccess.value = false
            }, 3000)
        }
    }
</script>

<template>
    <div class="row">
        <div class="row">
            <div class="col">
                <h1>
                    <FontAwesomeIcon icon="fa-solid fa-bullhorn"/>
                    Ordre de fabrication
                    <span v-if="isProductionWriterOrAdmin" class="btn-float-right">
                        <AppBtn
                            variant="success"
                            data-bs-toggle="modal"
                            :data-bs-target="target">
                            <Fa icon="plus"/>
                            Créer
                        </AppBtn>
                    </span>
                </h1>
            </div>
        </div>
        <div v-if="creationSuccess" class="row d-flex">
            <div class="bg-success text-white text-center">
                OF bien créé
            </div>
        </div>
        <AppModal
            ref="createModalRef"
            :id="modalId"
            class="four"
            :title="title">
            <AppManufacturingOrderCreate @created="onCreated"/>
        </AppModal>
        <div class="col">
            <AppCardableTable
                :fields="fields"
                icon="user-tag"
                :machine="machineManufacturingOrders"
                :store="manufacturingOrders"/>
        </div>
    </div>
</template>
