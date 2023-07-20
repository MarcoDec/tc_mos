<script setup>
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import {useRoute} from 'vue-router'
    import AppProductShowInlist from './bottom/AppProductShowInlist.vue'

    const route = useRoute()
</script>

<template>
    <AppShowGuiGen>
        <template #gui-left>
            coucou
        </template>
        <template #gui-bottom>
            <AppSuspense><AppProductShowInlist/></AppSuspense>
        </template>
        <template #gui-right>
            {{ route.params.id_product }}
        </template>
    </AppShowGuiGen>
</template>

