<script setup>
    import AppEmployeeFormShow from './AppEmployeeFormShow.vue'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import AppSuspense from '../../../AppSuspense.vue'
    import AppEmployeeShowInlist from './bottom/AppEmployeeShowInlist.vue'
    import {useEmployeeStore} from '../../../../stores/hr/employee/employees'
    import {useRouter, useRoute} from 'vue-router'
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
    import AppImg from '../../../AppImg.vue'
    import AppBtn from '../../../AppBtn.vue'
    import {onBeforeMount, ref} from 'vue'
    import AppShowEmployeeTabGeneral from './tabs/AppShowEmployeeTabGeneral.vue'

    const route = useRoute()
    const idEmployee = Number(route.params.id_employee)
    const fetchEmployeeStore = useEmployeeStore()
    const beforeMountDataLoaded = ref(false)
    const modeDetail = ref(true)
    const keyTitle = ref(0)
    const keyTabs = ref(0)
    const isFullScreen = ref(false)

    const imageUpdateUrl = `/api/employees/${idEmployee}/image`

    const requestDetails = () => {
        modeDetail.value = true
    }
    const requestExploitation = () => {
        modeDetail.value = false
    }

    function updateStores() {
        const promises = []
        // console.log('onBeforeMount')
        promises.push(fetchEmployeeStore.fetchOne(idEmployee))
        promises.push(fetchEmployeeStore.fetchAll())
        promises.push(fetchEmployeeStore.fetchTeams())
        Promise.all(promises).then(() => {
            // console.debug('employee', fetchEmployeeStore.employee)
            beforeMountDataLoaded.value = true
        })
    }

    onBeforeMount(() => {
        updateStores()
    })
    const onUpdated = () => {
        updateStores()
    }
    const onImageUpdate = () => {
        window.location.reload()
    }
    const activateFullScreen = () => {
        isFullScreen.value = true
    }
    const deactivateFullScreen = () => {
        isFullScreen.value = false
    }

    const router = useRouter()
    function goBack() {
        router.push({name: 'employee-list'})
    }
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen v-if="beforeMountDataLoaded">
            <template #gui-left>
                <div :key="`title-${keyTitle}`" class="bg-white border-1 p-1">
                    <button class="text-dark" @click="goBack">
                        <FontAwesomeIcon icon="user-tag"/>
                    </button>
                    <b>Employee
                        <span v-if="fetchEmployeeStore.employee.matricule !== null">({{ fetchEmployeeStore.employee.matricule }})</span></b>: {{ fetchEmployeeStore.employee.name }}
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
                    <AppSuspense><AppShowEmployeeTabGeneral :key="`form-${keyTabs}`" class="width70" @updated="onUpdated"/></AppSuspense>
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
