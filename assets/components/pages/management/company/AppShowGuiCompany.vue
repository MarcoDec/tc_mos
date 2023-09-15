<script setup>
    // import AppCompanyFormShow from './AppCompanyFormShow.vue'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    // import {useCompanyListStore} from '../../../../stores/company/companys'
    import useOptions from '../../../../stores/option/options'
    // import {useRoute} from 'vue-router'
    import AppCompanyShowInlist from './AppCompanyShowInlist.vue'

    // const route = useRoute()
    // const idCompany = Number(route.params.id_company)
    const fetchUnits = useOptions('units')
    // const useFetchCompanyStore = useCompanyListStore()
    fetchUnits.fetchOp()
    // useFetchCompanyStore.fetchOne(idCompany)
</script>

<template>
    <AppShowGuiGen>
        <template #gui-header>
            <!-- <div v-if="useFetchCompanyStore.isLoaded" class="bg-white border-1 border-dark">
                <b>{{ useFetchCompanyStore.company.code }}</b>: {{ useFetchCompanyStore.company.name }}
            </div> -->
        </template>
        <template #gui-left>
            <!-- <AppSuspense><AppCompanyFormShow v-if="useFetchCompanyStore.isLoaded && fetchUnits.isLoaded"/></AppSuspense> -->
        </template>
        <template #gui-bottom>
            <AppSuspense><AppCompanyShowInlist/></AppSuspense>
        </template>
        <template #gui-right/>
    </AppShowGuiGen>
</template>

<style>
    .border-dark {
        border-bottom: 1px solid grey;
    }
</style>
