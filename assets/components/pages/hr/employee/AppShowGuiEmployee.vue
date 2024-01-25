<script setup>
    import AppEmployeeFormShow from './AppEmployeeFormShow.vue'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import AppSuspense from '../../../AppSuspense.vue'
    import AppEmployeeShowInlist from './bottom/AppEmployeeShowInlist.vue'
    import {useEmployeeStore} from '../../../../stores/hr/employee/employees'
    import {useRoute} from 'vue-router'
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
    import AppImg from '../../../AppImg.vue'
    import AppBtn from '../../../AppBtn.vue'
    import AppShowComponentTabGeneral from '../../purchase/component/show/left/AppShowComponentTabGeneral.vue'
    import {onBeforeMount, ref} from 'vue'

    const route = useRoute()
    const idEmployee = Number(route.params.id_employee)
    const fetchEmployeeStore = useEmployeeStore()
    const beforeMountDataLoaded = ref(false)
    const modeDetail = ref(true)
    const keyTitle = ref(0)
    const keyTabs = ref(0)
    const isFullScreen = ref(false)

    const imageUpdateUrl = `/api/suppliers/${idEmployee}/image`

    const requestDetails = () => {
        modeDetail.value = true
    }
    const requestExploitation = () => {
        modeDetail.value = false
    }

    onBeforeMount(() => {
        const promises = []
        console.log('onBeforeMount')
        promises.push(fetchEmployeeStore.fetchOne(idEmployee))
        promises.push(fetchEmployeeStore.fetchAll())
        promises.push(fetchEmployeeStore.fetchTeams())
        Promise.all(promises).then(() => {
            beforeMountDataLoaded.value = true
        })
    })
    const onUpdated = () => {
        // console.log('onUpdated')
        const promises = []
        promises.push(fetchEmployeeStore.fetchOne(idEmployee))
        promises.push(fetchEmployeeStore.fetchAll())
        promises.push(fetchEmployeeStore.fetchTeams())
        Promise.all(promises).then(() => {
            beforeMountDataLoaded.value = true
        })
    }
    const onImageUpdate = () => {
        // console.log('onImageUpdate')
        fetchEmployeeStore.fetchOne(idEmployee)
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
                    <b>Employee ({{ fetchEmployeeStore.employee.id }})</b>: {{ fetchEmployeeStore.employee.name }}
                    <span class="btn-float-right">
                        <AppBtn :class="{'selected-detail': modeDetail}" label="Détails" icon="eye" variant="secondary" @click="requestDetails"/>
                        <AppBtn :class="{'selected-detail': !modeDetail}" label="Exploitation" icon="industry" variant="secondary" @click="requestExploitation"/>
                    </span>
                </div>
                <div class="d-flex flex-row">
                    <AppImg
                        class="width30"
                        :file-path="fetchEmployeeStore.employee.filePath"
                        :image-update-url="imageUpdateUrl"
                        @update:file-path="onImageUpdate"/>
                    <AppSuspense><AppShowComponentTabGeneral :key="`form-${keyTabs}`" class="width70" @updated="onUpdated"/></AppSuspense>
                </div>
            </template>
            <template #gui-bottom>
                <div :class="{'full-screen': isFullScreen}" class="bg-warning-subtle font-small">
                    <div class="full-visible-width">
                        <AppSuspense>
                            <AppEmployeeFormShow v-if="modeDetail" :key="`formtab-${keyTabs}`" class="width100"/>
                            <AppEmployeeShowInlist v-else :key="`formlist-${keyTabs}`" class="width100"/>
                        </AppSuspense>
                    </div>
                    <span>
                        <FontAwesomeIcon v-if="isFullScreen" icon="fa-solid fa-magnifying-glass-minus" @click="deactivateFullScreen"/>
                        <FontAwesomeIcon v-else icon="fa-solid fa-magnifying-glass-plus" @click="activateFullScreen"/>
                    </span>
                </div>
            </template>
            <template #gui-right>
                <!--            {{ route.params.id_employee }}-->
            </template>
        </AppShowGuiGen>
    </AppSuspense>
</template>
