<script setup>
    import AppTablePage from './AppTablePage.vue'
    import {useSlots} from '../../../composable/table'

    const props = defineProps({
        brands: {type: Boolean},
        disableAdd: {type: Boolean},
        disableRemove: {type: Boolean},
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        sort: {required: true, type: Object},
        title: {required: true, type: String}
    })
    const {slots} = useSlots(props.fields)
</script>

<template>
    <AppSuspense>
        <AppTablePage
            :brands="brands"
            :disable-add="disableAdd"
            :disable-remove="disableRemove"
            :fields="fields"
            :icon="icon"
            :sort="sort"
            :title="title">
            <template v-for="s in slots" :key="s.name" #[s.slot]="args">
                <slot :name="s.slot" v-bind="args"/>
            </template>
        </AppTablePage>
    </AppSuspense>
</template>
