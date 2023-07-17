<script setup>
    import AppTablePage from './AppTablePage.vue'
    import {useSlots} from '../../../composable/table'

    const props = defineProps({
        apiBaseRoute: {default: '', required: true, type: String},
        apiTypedRoutes: {
            default: () => {
                const obj = {}
                return obj
            },
            type: Object
        },
        brands: {type: Boolean},
        disableAdd: {type: Boolean},
        disableRemove: {type: Boolean},
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        readFilter: {default: '', required: false, type: String},
        sort: {required: true, type: Object},
        title: {required: true, type: String}
    })
    const {slots} = useSlots(props.fields)
</script>

<template>
    <AppSuspense>
        <AppTablePage
            :api-base-route="apiBaseRoute"
            :api-typed-routes="apiTypedRoutes"
            :brands="brands"
            :disable-add="disableAdd"
            :disable-remove="disableRemove"
            :fields="fields"
            :icon="icon"
            :read-filter="readFilter"
            :sort="sort"
            :title="title">
            <template v-for="s in slots" :key="s.name" #[s.slot]="args">
                <slot :name="s.slot" v-bind="args"/>
            </template>
        </AppTablePage>
    </AppSuspense>
</template>
