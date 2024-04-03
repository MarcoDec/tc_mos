<script setup>
    import AppShowGuiGen from '../../../AppShowGuiGen.vue'
    import AppSupplierFormShow from './AppSupplierFormShow.vue'
    import AppSupplierShowInlist from './bottom/AppSupplierShowInlist.vue'
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
    import AppImg from '../../../../AppImg.vue'
    import AppBtn from '../../../../AppBtn.vue'
    import AppSuspense from '../../../../AppSuspense.vue'
    import {computed, onBeforeMount, ref} from 'vue'
    import {useSuppliersStore} from '../../../../../stores/purchase/supplier/suppliers'
    import {useRoute} from 'vue-router'
    import AppSupplierShowTabGeneral from './tabs/AppSupplierShowTabGeneral.vue'
    import AppWorkflowShow from '../../../../workflow/AppWorkflowShow.vue'

    const isFullScreen = ref(false)

    const beforeMountDataLoaded = ref(false)
    const keyTitle = ref(0)
    const fetchSupplierStore = useSuppliersStore()
    const route = useRoute()
    const idSupplier = Number(route.params.id_supplier)
    const iriSupplier = ref('')
    const keyTabs = ref(0)
    const modeDetail = ref(true)
    const imageUpdateUrl = `/api/suppliers/${idSupplier}/image`
    const filePath = computed(() => `${fetchSupplierStore.supplier.filePath}?${Date.now()}`)

    onBeforeMount(() => {
        // console.log('onBeforeMount start')
        fetchSupplierStore.fetchOne(idSupplier).then(() => {
            // console.log('informations fournisseur chargées')
            iriSupplier.value = fetchSupplierStore.supplier['@id']
            beforeMountDataLoaded.value = true
        })
        // console.log('onBeforeMount end')
    })
    const onUpdated = () => {
        keyTitle.value++
    }
    const onImageUpdate = () => {
        window.location.reload()
    }
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
    /**
     * v-if="beforeMountDataLoaded"
     */
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen v-if="beforeMountDataLoaded">
            <template #gui-left>
                <div :key="`title-${keyTitle}`" class="bg-white border-1 p-1">
                    <div class="d-flex flex-row">
                        <div>
                            <FontAwesomeIcon icon="user-tie"/>
                            <b>{{ fetchSupplierStore.supplier.id }}</b>: {{ fetchSupplierStore.supplier.name }}
                        </div>
                        <AppSuspense>
                            <AppWorkflowShow :workflow-to-show="['supplier', 'blocker']" :item-iri="iriSupplier"/>
                        </AppSuspense>
                        <span class="ml-auto">
                            <AppBtn :class="{'selected-detail': modeDetail}" label="Détails" icon="eye" variant="secondary" @click="requestDetails"/>
                            <AppBtn :class="{'selected-detail': !modeDetail}" label="Exploitation" icon="industry" variant="secondary" @click="requestExploitation"/>
                        </span>
                    </div>
                </div>
                <div class="d-flex flex-row">
                    <AppImg
                        :key="`img-${keyTabs}`"
                        class="width30"
                        :file-path="filePath"
                        :image-update-url="imageUpdateUrl"
                        @update:file-path="onImageUpdate"/>
                    <AppSuspense>
                        <AppSupplierShowTabGeneral
                            :key="`form-${keyTabs}`"
                            class="width70"
                            :data-customers="fetchSupplierStore.supplier"
                            @updated="onUpdated"/>
                    </AppSuspense>
                </div>
            </template>
            <template #gui-bottom>
                <div :class="{'full-screen': isFullScreen}" class="bg-warning-subtle font-small">
                    <div class="full-visible-width">
                        <AppSuspense>
                            <AppSupplierFormShow v-if="modeDetail" :key="`formtab-${keyTabs}`" class="width100"/>
                            <AppSupplierShowInlist v-else :key="`formlist-${keyTabs}`" class="width100"/>
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

<style>
    .border-dark {
        border-bottom: 1px solid grey;
    }
</style>
