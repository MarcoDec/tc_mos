<script lang="ts" setup>
    import {defineProps, provide} from 'vue'
    import type {FormValues} from '../../types/bootstrap-5'
    import type {TableField} from '../../types/app-collection-table'

    const props = defineProps<{fields: TableField, id: string, items: FormValues[]}>()
    provide('fields', props.fields)
    provide('table-id', props.id)
</script>

<template>
    <table :id="id" class="table table-bordered table-hover table-striped">
        <AppCollectionTableHeaders/>
        <AppCollectionTableItems :items="items"/>
    </table>
    <AppPagination :count="100" :current="2" class="float-end"/>
</template>
