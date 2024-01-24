<script setup>
    import AppShowGuiGen from '../../../AppShowGuiGen.vue'
    import AppSupplierFormShow from './AppSupplierFormShow.vue'
    import AppSupplierShowInlist from './bottom/AppSupplierShowInlist.vue'
    // import {useRoute} from 'vue-router'
    //
    // const route = useRoute()
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen>
            <template #gui-header>
                <div class="bg-white border-1 border-dark">
                    Supplier
                </div>
            </template>
            <template #gui-left>
                <AppSuspense> <AppSupplierFormShow/></AppSuspense>
            </template>
            <template #gui-bottom>
                <AppSuspense>
                    <AppSupplierShowInlist/>
                </AppSuspense>
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
