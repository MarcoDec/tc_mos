<script setup>
    import api from '../../../../api'
    import {ref} from 'vue'
    // import AppCompanyShowInlist from './AppCompanyShowInlist.vue'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import AppSuspense from '../../../AppSuspense.vue'
    import Fa from '../../../Fa'
    // import {onMounted, onUnmounted} from 'vue'
    // import {useCompanyStore} from '../../../../stores/management/companies/companies'
    import {useRoute} from 'vue-router'

    defineProps({
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
    // const emits = defineEmits(['unMounted', 'mounted', 'vnode_unmounted'])
    const route = useRoute()
    const idCompany = Number(route.params.id_company)
    const companyData = ref({})
    const company = api(`/api/companies/${idCompany}, 'GET`)
    company.then(item => {
        companyData.value = item
    })
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen>
            <template #gui-header>
                <AppSuspense>
                    <div class="bg-white">
                        <h1>
                            <button class="text-dark">
                                <Fa :icon="icon"/>
                            </button>
                            {{ title }}: {{ companyData.name }}
                        </h1>
                    </div>
                </AppSuspense>
            </template>
            <template #gui-left>
                <AppSuspense>LEFT</AppSuspense>
            </template>
            <template #gui-right>
                <AppSuspense>RIGHT</AppSuspense>
            </template>
            <template #gui-bottom>
                <AppSuspense>BOTTOM<!--<AppCompanyShowInlist/>--></AppSuspense>
            </template>
        </AppShowGuiGen>
    </AppSuspense>
</template>

<style>
    .border-dark {
        border-bottom: 1px solid grey;
    }
</style>
