<script setup>
    import AppCustomerFormShow from './AppCustomerFormShow.vue'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import AppSuspense from '../../../AppSuspense.vue'
    import {useCustomerStore} from '../../../../stores/selling/customers/customers'
    import AppCustomerShowInlist from './bottom/AppCustomerShowInlist.vue'
    import {useRoute} from 'vue-router'
    import {onBeforeMount, ref} from "vue";
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import AppBtn from "../../../AppBtn.vue";
    import AppImg from "../../../AppImg.vue";
    import AppComponentFormShow from "../../purchase/component/show/AppComponentFormShow.vue";
    import AppComponentShowInlist from "../../purchase/component/show/AppComponentShowInlist.vue";
    import AppShowCustomerTabGeneral from "./tabs/AppShowCustomerTabGeneral.vue";
    import AppShowComponentTabGeneral from "../../purchase/component/show/left/AppShowComponentTabGeneral.vue";

    const route = useRoute()
    const idCustomer = Number(route.params.id_customer)
    const fetchCustomerStore = useCustomerStore()
    const beforeMountDataLoaded = ref(false)
    const keyTitle = ref(0)
    const modeDetail = ref(true)
    const isFullScreen = ref(false)
    const keyTabs = ref(0)
    const imageUpdateUrl = `/api/customers/${idCustomer}/image`

    onBeforeMount(() => {
        fetchCustomerStore.fetchOne(idCustomer).then(() => {
            beforeMountDataLoaded.value = true
        })
    })
    const onUpdated = () => {
        keyTitle.value++
    }
    const onImageUpdate = () => {
        // console.log('onImageUpdate')
        fetchCustomerStore.fetchOne(idCustomer)
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
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen v-if="beforeMountDataLoaded">
            <template #gui-left>
                <div :key="`title-${keyTitle}`" class="bg-white border-1 p-1">
                    <FontAwesomeIcon icon="puzzle-piece"/>
                    <b>{{ fetchCustomerStore.customer.id }}</b>: {{fetchCustomerStore.customer.name }}
                    <span class="btn-float-right">
                        <AppBtn :class="{'selected-detail': modeDetail}" label="Détails" icon="eye" variant="secondary" @click="requestDetails"/>
                        <AppBtn :class="{'selected-detail': !modeDetail}" label="Exploitation" icon="industry" variant="secondary" @click="requestExploitation"/>
                    </span>
                </div>
                <div class="d-flex flex-row">
                    <AppImg
                        class="width30"
                        :file-path="fetchCustomerStore.customer.filePath"
                        :image-update-url="imageUpdateUrl"
                        @update:file-path="onImageUpdate"/>
                    <AppSuspense>
                        <AppShowCustomerTabGeneral
                            :key="`form-${keyTabs}`"
                            class="width70"
                            :data-customers="fetchCustomerStore.customer"
                            @updated="onUpdated"/>
                    </AppSuspense>
                </div>
            </template>
            <template #gui-bottom>
                <div :class="{'full-screen': isFullScreen}" class="bg-warning-subtle font-small">
                    <div class="full-visible-width">
                        <AppSuspense>
                            <AppCustomerFormShow v-if="modeDetail" :key="`formtab-${keyTabs}`" class="width100"/>
                            <AppCustomerShowInlist v-else :key="`formlist-${keyTabs}`" class="width100"/>
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
