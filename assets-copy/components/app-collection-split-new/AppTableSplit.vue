<script lang="ts" setup>
    import {defineProps, provide} from 'vue'
    import type {Items} from '../../store/supplierItems/supplierItem/getters'
    import type {PropType} from 'vue'
    import type {TableField} from '../../types/app-collection-table'

    const props = defineProps({
        fields: {required: true, type: Object as PropType<TableField>},
        item: {required: true, type: Object as PropType<Items>}
    })
    provide('fields', props.fields)
</script>

<template>
    <table class="table table-bordered table-hover table-striped">
        <AppTableHeaders/>
        <AppTableItems :item="item"/>
    </table>
</template>
