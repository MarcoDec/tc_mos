<script setup>
    import {onBeforeUnmount} from 'vue'
    import AppComponentFormShow from './AppComponentFormShow.vue'
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import {useComponentListStore} from '../../../../stores/purchase/component/components'
    import useOptions from '../../../../stores/option/options'
    import {useRoute} from 'vue-router'
    import AppComponentShowInlist from './AppComponentShowInlist.vue'

    const route = useRoute()
    const idComponent = Number(route.params.id_component)
    const fetchUnits = useOptions('units')
    const useFetchComponentStore = useComponentListStore()
    fetchUnits.fetchOp()
    useFetchComponentStore.fetchOne(idComponent)
    onBeforeUnmount(() => {
        useFetchComponentStore.reset()
    })
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen>
            <template #gui-header>
                <div class="bg-white border-1 border-dark" style="position:relative; top:0px;left:0px;">
<!--                    <font-awesome-icon icon="puzzle-piece"/>-->
                    <b>{{ useFetchComponentStore.component.code }}</b>: {{ useFetchComponentStore.component.name }}
                </div>
            </template>
            <template #gui-left>
                <AppSuspense><AppComponentFormShow v-if="useFetchComponentStore.isLoaded && fetchUnits.isLoaded"/></AppSuspense>
            </template>
            <template #gui-bottom>
                <AppSuspense><AppComponentShowInlist/></AppSuspense>
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
